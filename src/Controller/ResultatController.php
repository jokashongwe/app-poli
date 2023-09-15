<?php

namespace App\Controller;

use App\Repository\ResultatRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatController extends AbstractController
{
    #[Route('/resultat/{id}', name: 'app_resultat')]
    public function index(Request $request, ManagerRegistry $doctrine, ResultatRepository $resultatRepository, $id): Response
    {
        $resultats = $resultatRepository->findBy(['candidat' => $id]);
        return $this->renderForm('resultat/index.html.twig', [
            'controller_name' => 'ResultatController',
            'resultats' => $resultats
        ]);
    }
}
