<?php

namespace App\Controller;


use App\Entity\Province;
use App\Entity\Tag;
use App\Form\Type\groupeType;
use App\Form\Type\TagType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{


    #[Route('/tags/new', name: 'tag_new')]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {

        $groupe = new Tag();
        $user = $this->getUser();
        $organisation = $user->getOrganisation();

        $groupes = $doctrine->getRepository(Tag::class)->findBy(['organisation' => $organisation]);
        
        
        $form = $this->createForm(TagType::class, $groupe);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $groupe = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($groupe);
            $entityManager->flush();
            

            return $this->redirectToRoute('tag_new');
        }
            

        return $this->renderForm('tag/index.html.twig', [
            'controller_name' => 'TagController',
            'form'          => $form,
            'tags' => $groupes,
            'toast' => null
        ]);
    }

    #[Route('/tags/update/{id}', name: 'tag_update')]
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {

        $groupe = $doctrine->getRepository(Tag::class)->find($id);
        
        
        $form = $this->createForm(TagType::class, $groupe);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $groupe2 = $form->getData();

            $groupe->setNom($groupe2->getNom());
            $groupe->setProvince($groupe2->getProvince());
            $groupe->setgroupe($groupe2->getgroupe());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($groupe);
            $entityManager->flush();
            

            return $this->redirectToRoute('tag_new');
        }
            
    }
}
