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
    public function index(ResultatRepository $resultatRepository, $id): Response
    {
        $resultats = $resultatRepository->findBy(['candidat' => $id]);
        $total = ['votants' => 0, 'voix' => 0];
        $totalParBureauVote = [];
        foreach($resultats as $resultat){
            $total['votants'] += $resultat->getNombreVotant();
            $total['voix'] += $resultat->getNombreVoix();
            $codeBV = 'B-' . $resultat->getCodeBV();
            if(!array_key_exists($codeBV, $totalParBureauVote)){
                $totalParBureauVote[$codeBV] = 0;
            }
            $totalParBureauVote[$codeBV] += $resultat->getNombreVoix();
        }
        return $this->renderForm('resultat/index.html.twig', [
            'controller_name' => 'ResultatController',
            'resultats' => $resultats,
            'rapport' => $total,
            'rapport_bv' => json_encode(array_values($totalParBureauVote)),
            'bureau_votes' => json_encode(array_keys($totalParBureauVote))
        ]);
    }
}
