<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Class PostCandidateController for API
 */
#[AsController]
class PostCandidateController extends AbstractController
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
     * @param Candidate $candidate
     * @return Candidate
     * @throws \Exception
     */
    public function __invoke(Candidate $candidate): Candidate
    {
        $content = $this->_request->toArray(); // get content

        if(isset($content["lastName"], $content["firstName"])) {
            // create user
            $user = new User();
            $user->setLastName($content["lastName"]);
            $user->setFirstName($content["firstName"]);
            $user->setCandidate($candidate);
            $this->_entityManager->persist($user);

            return $candidate;
        }

        throw new \Error('Error : you need to set lastName and firstName');
    }
}