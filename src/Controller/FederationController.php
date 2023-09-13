<?php

namespace App\Controller;

use App\Entity\Federation;
use App\Entity\Province;
use App\Form\Type\FederationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FederationController extends AbstractController
{


    #[Route('/federation/new', name: 'federation_new')]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {

        $federation = new Federation();

        $federations = $doctrine->getRepository(Federation::class)->findAll();
        
        
        $form = $this->createForm(FederationType::class, $federation);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $federation = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($federation);
            $entityManager->flush();
            

            return $this->redirectToRoute('federation_new');
        }
            

        return $this->renderForm('federation/index.html.twig', [
            'controller_name' => 'FederationController',
            'form'          => $form,
            'federations' => $federations,
            'toast' => null
        ]);
    }

    #[Route('/federation/update/{id}', name: 'federation_update')]
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {

        $federation = $doctrine->getRepository(Federation::class)->find($id);
        
        
        $form = $this->createForm(FederationType::class, $federation);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $federation2 = $form->getData();

            $federation->setNom($federation2->getNom());
            $federation->setProvince($federation2->getProvince());
            $federation->setFederation($federation2->getFederation());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($federation);
            $entityManager->flush();
            

            return $this->redirectToRoute('federation_new');
        }
            
    }
}
