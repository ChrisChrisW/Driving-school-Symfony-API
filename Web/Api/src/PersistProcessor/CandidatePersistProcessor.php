<?php

namespace App\PersistProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Candidate;
use App\Entity\Formula;
use App\Entity\FormulaCodeDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class CandidatePersistProcessor
 */
class CandidatePersistProcessor implements ProcessorInterface
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
     * @param $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return Candidate
     * @throws \Exception
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) : Candidate
    {
        $content = $this->_request->toArray(); // get content

        $candidateRepository = $this->_entityManager->getRepository(Candidate::class);
        $candidate = $candidateRepository->findOneByEmail($content["currentEmail"]);

        if($candidate) {
            if(isset($content['address'])) {
                $candidate->setAddress($content['address']);
            }

            if(isset($content['age'])) {
                $candidate->setAge($content['age']);
            }

            // Set different Code formula
            if (isset($content['formuleCode'], $content['formuleCodeIllimite'], $content['formuleConduite']) || isset($content['formuleCode'], $content['formuleCodeIllimite']) || isset($content['formuleConduite'], $content['formuleCodeIllimite']) || isset($content['formuleCode'], $content['formuleConduite'])) {
                throw new \Error("Vous ne pouvez pas demandé deux cours en même temps !");
            }
            if (isset($content['formuleCode']) || isset($content['formuleCodeIllimite'])) {
                foreach ($candidate->getFormulaCodeDates() as $formulaCodeDate) {
                    if($formulaCodeDate->getEndDate() === null) {
                        throw new \Error("Vous avez une formule illimité du coup vous n'avez pas besoin de prendre une nouvelle formule !");
                    }
                }
            }

            // Enter User Entity
            $user = $candidate->getIdentity();
            if($user) {
                $userUpdateInfo = false;

                if(isset($content['lastName'])) {
                    $user->setLastName($content['lastName']);
                    $userUpdateInfo = true;
                }

                if(isset($content['firstName'])) {
                    $user->setFirstName($content['firstName']);
                    $userUpdateInfo = true;
                }

                if($userUpdateInfo) {
                    $this->_entityManager->persist($user);
                    $this->_entityManager->flush();
                }
            } else {
                throw new \Error("Vous vous êtes trompés d'utilisateur !");
            }
            if(isset($content['address'])) {
                $candidate->setAddress($content['address']);
            }

            if(isset($content['formuleCode']) && !isset($content['formuleCodeIllimite']) && $content['formuleCode']) {
                $formulaRepository = $this->_entityManager->getRepository(Formula::class);
                $formula = $formulaRepository->findOneBySlug("formule-code");
                $formulaCodeDate = new FormulaCodeDate();
                $formulaCodeDate->setStartDate(new \DateTime($content['startDate']));
                // after 6 months
                $nextDate = new \DateTime($content['startDate']);
                $nextDate->add(new \DateInterval('P6M'));
                $formulaCodeDate->setEndDate($nextDate);
                $formulaCodeDate->setCandidate($candidate);
                $formulaCodeDate->setFormula($formula);
                $this->_entityManager->persist($formulaCodeDate);
                $this->_entityManager->flush();
            }
            if(isset($content['formuleCodeIllimite']) && !isset($content['formuleCode']) && $content['formuleCodeIllimite']) {
                $formulaRepository = $this->_entityManager->getRepository(Formula::class);
                $formula = $formulaRepository->findOneBySlug("formule-code-illimite");
                $formulaCodeDate = new FormulaCodeDate();
                $formulaCodeDate->setStartDate(new \DateTime($content['startDate']));
                $formulaCodeDate->setCandidate($candidate);
                $formulaCodeDate->setFormula($formula);
                $this->_entityManager->persist($formulaCodeDate);
                $this->_entityManager->flush();
            }
            if(isset($content['formuleConduite']) && $content['formuleConduite']) {
                $formulaRepository = $this->_entityManager->getRepository(Formula::class);
                $formula = $formulaRepository->findOneBySlug($content["formuleConduite"]);

                if($formula->getDrivingFormula()) {
                    $candidate->addDrivingFormula($formula->getDrivingFormula());
                }
            }

            $this->_entityManager->persist($candidate);
            $this->_entityManager->flush();
            return $data;
        }

        throw new \Error("Vous vous êtes trompés de candidat !");
    }
}