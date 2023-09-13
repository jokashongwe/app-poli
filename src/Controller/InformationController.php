<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Federation;
use App\Entity\Membre;
use App\Repository\AllInfoRepository;
use App\Repository\CotisationRepository;
use App\Repository\MembreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InformationController extends AbstractController
{

    #[Route('/informations', name: 'information')]
    public function index(Request $request, ManagerRegistry $doctrine, AllInfoRepository $allInfoRepository): Response
    {
        $informations = $allInfoRepository->findAll();
        return $this->renderForm('information/index.html.twig', [
            'controller_name' => 'InformationController',
            'information' => empty($informations) ? null : $informations[0]
        ]);
    }
}
