<?php

namespace App\Controller;

use App\Entity\Formula;
use App\Entity\Trainer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * PostTrainerController for Api
 */
#[AsController]
class PostTrainerController extends AbstractController
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
     * @param Trainer $trainer
     * @return Trainer
     * @throws \Exception
     */
    public function __invoke(Trainer $trainer): Trainer
    {
        $content = $this->_request->toArray(); // get content

        if(isset($content["lastName"], $content["firstName"])) {
            $formulaRepository = $this->_entityManager->getRepository(Formula::class);
            $formula = $formulaRepository->findOneBySlug($content["formulaSlug"]);

            if($formula) {
                $trainer->addFormula($formula);
            } else {
                throw new \Error('Error : you need to put a correct formula.');
            }

            // create user
            $user = new User();
            $user->setLastName($content["lastName"]);
            $user->setFirstName($content["firstName"]);
            $user->setTrainer($trainer);
            $this->_entityManager->persist($user);
            $this->_entityManager->flush();

            return $trainer;
        }

        throw new \Error('Error : you need to set lastName and firstName');
    }
}