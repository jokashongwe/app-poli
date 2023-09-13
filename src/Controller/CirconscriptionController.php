<?php

namespace App\Controller;

use App\Entity\Circonscription;
use App\Form\Type\CirconscriptionType;
use App\Repository\CirconscriptionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CirconscriptionController extends AbstractController
{
    #[Route('/circonscription', name: 'app_circonscription')]
    public function index(Request $request, ManagerRegistry $doctrine, CirconscriptionRepository $circonscriptionRepository): Response
    {
        $circonscription = new Circonscription();

        $form = $this->createForm(CirconscriptionType::class, $circonscription);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $circonscription = $form->getData();
            
            $entityManager = $doctrine->getManager();
            $entityManager->persist($circonscription);
            $entityManager->flush();


            return $this->redirectToRoute('app_circonscription');
        }


        return $this->renderForm('circonscription/index.html.twig', [
            'controller_name' => 'CirconscriptionController',
            'form' => $form,
            'circonscriptions' => $circonscriptionRepository->findAll()
        ]);
    }
}
