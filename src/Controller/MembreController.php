<?php

namespace App\Controller;

use App\Entity\Federation;
use App\Entity\Membre;
use App\Form\Type\MembreType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class MembreController extends AbstractController
{

    private function generateIdNumber(){
        $part01 = rand(3000, 9999);
        $part02 = rand(300000, 999999);
        $part03 = date("Y");
        return ''. $part01 .'/'. $part02 . '/' . $part03;
    }

    #[Route('/membre/new', name: 'membre_new')]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {

        $membre = new Membre();

        $membres = $doctrine->getRepository(Membre::class)->findAll();
        
        
        $form = $this->createForm(MembreType::class, $membre);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $membre = $form->getData();
            $membre->setNoidentification($this->generateIdNumber());
            $membre->setDateadhesion(new \DateTimeImmutable());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($membre);
            $entityManager->flush();
            

            return $this->redirectToRoute('membre_new');
        }
            

        return $this->renderForm('membre/index.html.twig', [
            'controller_name' => 'MembreController',
            'form'          => $form,
            'membres' => $membres
        ]);
    }

    #[Route('/membre/update/{id}', name: 'membre_update')]
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {

        $membre = new Membre();

        $membres = $doctrine->getRepository(Membre::class)->findAll();
        
        
        $form = $this->createForm(MembreType::class, $membre);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $membre2 = $form->getData();

            $membre->setNom($membre2->getNom());
            $membre->setPostnom($membre2->getPostnom());
            $membre->setPrenom($membre2->getPrenom());
            $membre->setTelephone($membre2->getTelephone());
            $membre->setGenre($membre2->getGenre());
            $membre->setDatenaissance($membre2->getDatenaissance());
            $membre->setAdresse($membre2->getAdresse());
            $membre->setFederation($membre2->getFederation());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($membre);
            $entityManager->flush();
            

            return $this->redirectToRoute('membre_new');
        }
            
    }
}
