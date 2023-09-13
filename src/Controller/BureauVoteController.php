<?php

namespace App\Controller;

use App\Entity\BureauVote;
use App\Form\Type\BureauVoteType;
use App\Repository\BureauVoteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BureauVoteController extends AbstractController
{
    #[Route('/bureau/vote', name: 'app_bureau_vote')]
    public function index(Request $request, ManagerRegistry $doctrine, BureauVoteRepository $bureauVoteRepository): Response
    {
        $bureauVote = new BureauVote();

        $form = $this->createForm(BureauVoteType::class, $bureauVote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $bureauVote = $form->getData();
            
            $entityManager = $doctrine->getManager();
            $entityManager->persist($bureauVote);
            $entityManager->flush();


            return $this->redirectToRoute('app_bureau_vote');
        }


        return $this->renderForm('bureau_vote/index.html.twig', [
            'controller_name' => 'BureauVoteControllers',
            'form' => $form,
            'bureau_votes' => $bureauVoteRepository->findAll()
        ]);
    }
}
