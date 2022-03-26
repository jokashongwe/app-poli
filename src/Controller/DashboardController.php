<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Federation;
use App\Entity\Membre;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private function compter(ManagerRegistry $em, $tableClass){
        return $em->getRepository($tableClass)->compter();
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(ManagerRegistry $entityManager): Response
    {

        //$federations = $entityManager->getRepository(Federation::class)->findAll();
        $federationCount = $this->compter($entityManager, Federation::class);
        $memberCount =  $this->compter($entityManager, Membre::class);
        $list = $entityManager->getRepository(Membre::class)->findBy(array(), null, 5, null);
        
        $membreData = $entityManager->getRepository(Membre::class)->countByProvince(null);
        $membreGenre = $entityManager->getRepository(Membre::class)->countByGenre(null);
        $total = $entityManager->getRepository(Cotisation::class)->countByAmount(null)[0]["total"];

        $membreLabels = [];
        $membreValues = [];

        $membreValuesChart2 = [];
        foreach($membreGenre as $membre)
        {
            array_push($membreValuesChart2, $membre['frequence']);
        }
        
        foreach($membreData as $membre)
        {
            array_push($membreLabels, $membre['nom']);
            array_push($membreValues, $membre['frequence']);
        }
        
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'federations' => $federationCount,
            'membreCount' => $memberCount,
            'membres' => $list,
            'membreLabels' => json_encode($membreLabels),
            'membreValues' => json_encode($membreValues),
            'membreValuesChart2' => json_encode($membreValuesChart2),
            'totalMontant' => is_null($total) ? 0 : $total
        ]);
    }
}
