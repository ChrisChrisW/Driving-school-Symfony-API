<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomepageController
 */
class HomepageController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->redirect('/api');
    }

    /**
     * @return Response
     */
    #[Route('/homepage', name: 'app_homepage')]
    public function homepage(): Response
    {
        return $this->redirect('/api');
    }

    /**
     * @return Response
     */
    #[Route('/accueil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->redirect('/api');
    }

    /**
     * @return Response
     */
    #[Route('/api_platform', name: 'api_platform')]
    public function api_platform(): Response
    {
        return $this->redirect('/api');
    }

    /**
     * @return Response
     */
    #[Route('/api-platform', name: 'api-platform')]
    public function api__platform(): Response
    {
        return $this->redirect('/api');
    }
}
