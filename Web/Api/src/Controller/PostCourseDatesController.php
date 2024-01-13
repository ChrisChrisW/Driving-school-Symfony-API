<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\CourseDates;
use App\Entity\Formula;
use App\Entity\Trainer;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Class PostCourseDatesController for API
 */
#[AsController]
class PostCourseDatesController extends AbstractController
{
    /**
     * @var Request|null
     */
    private ?Request $_request;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $_entityManager;

    /**
     * @param RequestStack $request
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RequestStack $request, EntityManagerInterface $entityManager)
    {
        $this->_request = $request->getCurrentRequest();
        $this->_entityManager = $entityManager;
    }

    /**
     * @param CourseDates $courseDate
     * @return CourseDates
     * @throws \Exception
     */
    public function __invoke(CourseDates $courseDate): CourseDates
    {
        $content = $this->_request->toArray(); // get content
        $currentDate = (new \DateTime('now'));

        if(!isset($content["startDate"], $content['endDate'])) {
            throw new \Error('Error : you need to set a Date ! ');
        }
        if($currentDate > (new \DateTime($content["startDate"]))) {
            throw new \Error('Error : you need to set a correct Date ! ');
        }

        $date = new \DateTime($content["startDate"]);
        $dateAfter = new \DateTime($content["endDate"]);
        $diff = $date->diff($dateAfter);
        // check if endDate is greater than startDate
        if ($diff->format("%R") === "-") {
            throw new \Error('Error : endDate must be greater than startDate!');
        }

        // Check if the 4 days is correctly respected
        $date->modify("-4 days");
        $diff = $date->diff($currentDate);
        if(!$diff->invert) {
            throw new \Error('Error : you need to set a Date before 4 days ! ');
        }

        if(!isset($content["candidateEmail"])) {
            throw new \Error('Error : you need to set a candidateEmail');
        }

        $candidateRepository = $this->_entityManager->getRepository(Candidate::class);
        $candidate = $candidateRepository->findOneByEmail($content["candidateEmail"]);

        if(isset($content["formulaSlug"], $content["trainerNumSs"], $content["vehicleNumPlate"])) {
            // Classic Formula
            $formulaRepository = $this->_entityManager->getRepository(Formula::class);
            $formula = $formulaRepository->findOneBySlug($content["formulaSlug"]);

            // Check if the candidate has the min age
            if($candidate->getAge() < $formula->getMinAge()) {
                throw new \Error('Error : Only '.$formula->getMinAge().' year can take this formula.');
            }

            $vehicleRepository = $this->_entityManager->getRepository(Vehicle::class);
            $vehicle = $vehicleRepository->findOneByNumPlate($content["vehicleNumPlate"]);
            $isGoodFormulaVehicle = false;
            // check if the vehicle is in formula
            foreach ($formula->getDrivingFormula()->getVehicles() as $fVehicle) {
                if($fVehicle === $vehicle) {
                    $isGoodFormulaVehicle = true;
                    break;
                }
            }
            if(!$isGoodFormulaVehicle) {
                throw new \Error('Error : The vehicle is in a bad formula !');
            }

            // Check if the candidate has ever courses
            $courseDateRepository = $this->_entityManager->getRepository(CourseDates::class);
            $courses = $courseDateRepository->findNumberOfCoursesByCandidateAndFormula($content["candidateEmail"], $content["formulaSlug"]);
            $courseNumberOfHours = 0;
            foreach($courses as $course){
                $interval = date_diff($course['startDate'], $course['endDate']);
                $courseNumberOfHours += $interval->h;
            }
            if($courseNumberOfHours >= $formula->getDrivingFormula()->getNbHours()) {
                throw new \Error('Error : enough Courses, you have to much courses for this formula.');
            }

            // check if the car is already used
            $courses = $courseDateRepository->findByVehicleAndStartDateAndEndDate($vehicle->getNumPlate(), $content['startDate'], $content['endDate']);
            if($courses) {
                throw new \Error('Error : this car has already used by an another person !');
            }

            // Add Trainer
            $trainerRepository = $this->_entityManager->getRepository(Trainer::class);
            $trainer = $trainerRepository->findOneByNumSs($content["trainerNumSs"]);
            if(!$trainer) {
                throw new \Error('Error : you need to set a correct trainerNumSs');
            }
            // Check if the trainer is available
            foreach ($trainer->getCourseDates() as $trainerCourseDate) {
                if(($content["startDate"] === $trainerCourseDate->getStartDate()) && ($content["endDate"] === $trainerCourseDate->getEndDate())) {
                    throw new \Error('Error : The trainer is not available !');
                }
            }
            $courseDate->setTrainer($trainer);

            // Create CandidateHasFormulaAndTakesCourses if trainer has licence
            foreach ($trainer->getFormulas() as $trainerFormula) {
                if($trainerFormula === $formula) {
                    $courseDate->setCandidate($candidate);
                    $courseDate->setFormula($formula);
                    $courseDate->setVehicle($vehicle);

                    $courseDate->setIsConfirm(true); // confirm but if the trainer don't want to assist this course, set false

                    return $courseDate;
                }
            }

            throw new \Error("Error : you're trainer don't have this licence (formula).");

        } elseif (!isset($content['vehicleNumPlate'])) {
            // Code Formula

            // Classic Formula
            $formulaRepository = $this->_entityManager->getRepository(Formula::class);
            $formula = $formulaRepository->findOneBySlug($content["formulaSlug"]);

            // Add Trainer
            $trainerRepository = $this->_entityManager->getRepository(Trainer::class);
            $trainer = $trainerRepository->findOneByNumSs($content["trainerNumSs"]);
            if(!$trainer) {
                throw new \Error('Error : you need to set a correct trainerNumSs');
            }
            // Check if the trainer is available
            foreach ($trainer->getCourseDates() as $trainerCourseDate) {
                if(($content["startDate"] === $trainerCourseDate->getStartDate()) && ($content["endDate"] === $trainerCourseDate->getEndDate())) {
                    throw new \Error('Error : The trainer is not available !');
                }
            }
            $courseDate->setTrainer($trainer);

            // Create CandidateHasFormulaAndTakesCourses if trainer has licence
            foreach ($trainer->getFormulas() as $trainerFormula) {
                if($trainerFormula === $formula) {
                    $courseDate->setIsConfirm(true); // confirm but if the trainer don't want to assist this course, set false

                    foreach ($candidate->getFormulaCodeDates() as $formulaCodeDates) {
                        if($formulaCodeDates->getStartDate() <= $content["startDate"]) {
                            throw new \Error('Error : The formula code is not valid or not yet !');
                        }

                        // null => code illimité
                        // endDate => code classique
                        if(($formulaCodeDates->getEndDate() === null) || ($formulaCodeDates->getEndDate() > $content["endDate"])) {
                            $formula = $formulaCodeDates->getFormula();
                            // Check if the candidate has the min age
                            if($candidate->getAge() < $formula->getMinAge()) {
                                throw new \Error('Error : Only '.$formula->getMinAge().' year can take this formula.');
                            }

                            $courseDate->setCandidate($candidate);
                            $courseDate->setFormula($formula);

                            $courseDate->setIsConfirm(true); // confirm but if the trainer don't want to assist this course, set false

                            return $courseDate;
                        }
                    }
                }
            }


        }

        throw new \Error("Error : can't create a Course date ! (maybe forgot trainer)");
    }
}