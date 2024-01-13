<?php

namespace App\PersistProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\CourseDates;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/**
 * Class CourseDatesPersistProcessor
 */
class CourseDatesPersistProcessor implements ProcessorInterface
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
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @param RequestStack $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param MailerInterface $mailer
     */
    public function __construct(RequestStack $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, MailerInterface $mailer)
    {
        $this->_request = $request->getCurrentRequest();
        $this->_entityManager = $entityManager;
        $this->validator = $validator;
        $this->mailer = $mailer;
    }

    /**
     * @param $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return CourseDates
     * @throws TransportExceptionInterface|\Exception
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) : CourseDates
    {
        $content = $this->_request->toArray(); // get content

        $courseDateRepository = $this->_entityManager->getRepository(CourseDates::class);
        $courseDate = $courseDateRepository->find($data->getId());

        if($courseDate) {
            $currentDate = (new \DateTime('now'));
            $date = $courseDate->getStartDate();

            if(isset($content["isAchieve"]) && $courseDate->getIsConfirm()) {
                $courseDate->setIsAchieve(true);

                $this->_entityManager->persist($courseDate);
                $this->_entityManager->flush();

                return $data;
            }

            if($courseDate->getIsAchieve()) {
                // Check if candidate has a great justification and can delete the courses
                if($content["absenceJustification"]) {
                    $courseDate->setIsConfirm(false);
                    $courseDate->setIsAchieve(false);

                    $this->_entityManager->persist($courseDate);
                    $this->_entityManager->flush();

                    return $data;
                }

                throw new \Error("Impossible de motifier, le cours a déjà été accompli !");
            }
//            if(!$courseDate->getIsConfirm()){
//                throw new \Error("Impossible de motifier, le cours a déjà été annulé !");
//            }

            // Trainer Confirm course
            if(isset($content["isConfirm"]) && !$content["isConfirm"]) {
                $three_days_before = $date;
                $one_days_before = $date;
                $three_days_before->modify("-3 days");
                $one_days_before->modify("-1 days");

                if($courseDate->getIsRedirectedToAnotherTrainer()) {
                    $diff = $date->diff($currentDate);
                    if(!$diff->invert) {
                        $courseDate->setIsConfirm($content["isConfirm"]);

                        // TODO : Email send to candidate
                        try {
                            // Validate the request
                            $this->validator->validate($data);

                            $email = (new Email())
                                ->from($_ENV['MAILER_FROM'])
                                ->to($data->getCandidate()->getEmail())
                                ->subject('Course Cancellation')
                                ->html($this->renderView('emails/cancel_course.html.twig', [
                                    'course' => $data,
                                ]));

                            // Configure the transport
                            $transport = Transport::fromDsn(getenv('MAILER_DSN'));
                            // Create the mailer
                            $mailer = new Mailer($transport);
                            // Send the email
                            $sentEmail = $mailer->send($email);

                            // check if the email was sent successfully
                            if(count($sentEmail->getErrors()) > 0) {
                                // throw new \Error("Impossible d'annuler et d'envoyer un mail au candidat !\nError encountered while sending email: ". $sentEmail->getErrors());
                                echo "Impossible d'annuler et d'envoyer un mail au candidat !\nError encountered while sending email: ". $sentEmail->getErrors();
                                // log error or throw exception
                            }

                            // email sent successfully
                        } catch (\Exception $exception) {
                            // throw new \Error("Impossible d'annuler et d'envoyer un mail au candidat !\nError : ". $exception);
                            echo "Impossible d'annuler et d'envoyer un mail au candidat !\nError : ". $exception;
                        }

                        $this->_entityManager->persist($courseDate);
                        $this->_entityManager->flush();
                        return $data;
                    }

                    throw new \Error("Impossible d'annuler, le cours devait être annulé 1 jours au préalable !");
                }

                if($currentDate <= $three_days_before) {
                    $courseDate->setIsConfirm($content["isConfirm"]);

                    // Similar with Controller PostCourseDatesController => Create a courseDate
                    $trainerRepository = $this->_entityManager->getRepository(Trainer::class);
                    $trainers = $trainerRepository->findByFormulaSlug($courseDate->getFormula()->getSlug()); // trainers have formula skill

                    try {
                        foreach ($trainers as $trainer) {
                            // search a different trainer for this candidate
                            if($courseDate->getTrainer() !== $trainer) {
                                // TODO : error
                                foreach ($trainer->getCourseDates() as $trainerCourseDate) {
                                    // check if the trainer is available
                                    if(($courseDate->getStartDate() !== $trainerCourseDate->getStartDate()) && ($courseDate->getEndDate() !== $trainerCourseDate->getEndDate())) {
                                        // Make a new CourseDate
                                        $newCourseDate = new CourseDates();
                                        $newCourseDate->setStartDate($date);
                                        $newCourseDate->setEndDate($courseDate->getEndDate());
                                        $newCourseDate->setIsConfirm(true);
                                        $newCourseDate->setIsRedirectedToAnotherTrainer(true); // change trainer
                                        $newCourseDate->setFormula($courseDate->getFormula());
                                        $newCourseDate->setCandidate($courseDate->getCandidate());
                                        $newCourseDate->setTrainer($trainer);

                                        $this->_entityManager->persist($newCourseDate);
                                        $this->_entityManager->persist($courseDate);
                                        $this->_entityManager->flush();

                                        return $data;
                                    }
                                }
                            }
                        }
                    }
                    catch (\Exception $e) {
                        echo "Impossible de rédiriger le cours". $e;
                    }

                    $this->_entityManager->persist($courseDate);
                    $this->_entityManager->flush();
                    return $data;
                }

                throw new \Error("Impossible d'annuler, le cours devait être annulé 3 jours au préalable !");
            }

            // Achieve course
            if(isset($content["isAchieve"]) && $courseDate->getIsConfirm()) {
                $fifteen_minutes_later = strtotime("+900 seconds", strtotime($date));
                if($currentDate >= $fifteen_minutes_later) {
                    $courseDate->setIsAchieve($content["isAchieve"]);
                    $this->_entityManager->persist($courseDate);
                    $this->_entityManager->flush();

                    return $data;
                }

                throw new \Error("Vous ne pouvez pas terminé le cours parce que le cours n'a pas été encore effectué ou le candidat n'est pas encore arrivé en retard (15 min d'attente min) !");
            }

            throw new \Error("Vous avez oublié votre requête !");
        }

        throw new \Error("Vous vous êtes trompés de cours !");
    }
}