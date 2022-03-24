<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FederationController extends AbstractController
{
    #[Route('/federation', name: 'app_federation')]
    public function index(): Response
    {
        return $this->render('federation/index.html.twig', [
            'controller_name' => 'FederationController',
        ]);
    }
}
