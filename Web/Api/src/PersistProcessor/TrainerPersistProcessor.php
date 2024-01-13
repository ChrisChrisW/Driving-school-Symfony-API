<?php

namespace App\PersistProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Formula;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class TrainerPersistProcessor
 */
class TrainerPersistProcessor implements ProcessorInterface
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
     * @return Trainer
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) : Trainer
    {
        $content = $this->_request->toArray(); // get content

        $trainerRepository = $this->_entityManager->getRepository(Trainer::class);
        $trainer = $trainerRepository->findOneByNumSs($content["currentNumSs"]);

        if($trainer) {
            // Enter User Entity
            $user = $trainer->getIdentity();
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

            if(isset($content['formulaSlug'])) {
                $formulaRepository = $this->_entityManager->getRepository(Formula::class);
                $formula = $formulaRepository->findOneBySlug($content['formulaSlug']);
                $trainer->addFormula($formula);
            }

            if(isset($content['removeFormula'])) {
                $formulaRepository = $this->_entityManager->getRepository(Formula::class);
                $formula = $formulaRepository->findOneBySlug($content['removeFormula']);
                $trainer->removeFormula($formula);
            }

            $this->_entityManager->persist($trainer);
            $this->_entityManager->flush();
            return $data;
        }

        throw new \Error("Vous vous êtes trompés de trainer !");
    }
}