<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Federation;
use App\Entity\Membre;
use App\Repository\CotisationRepository;
use App\Repository\MembreRepository;
use App\Service\MessageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private function compter(ManagerRegistry $em, $tableClass)
    {
        return $em->getRepository($tableClass)->compter();
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(ManagerRegistry $entityManager, MembreRepository $membreRepository, CotisationRepository $cotisationRepository): Response
    {
        $service = new MessageService($this->getParameter('app.bulksmstoken'));
        $result = $service->getCredits();
        $response = json_decode($result['server_response'], true);
        $credits = 0;
        if(!empty($response)){
            $credits = $response['credits']['balance'];
        }
        $licence = $this->getParameter('app.systemlicence');
        //$federations = $entityManager->getRepository(Federation::class)->findAll();
        $federationCount = $this->compter($entityManager, Federation::class);
        $memberCount =  $this->compter($entityManager, Membre::class);
        $list = $membreRepository->findBy(array(), null, 5, null);

        $membreData = $membreRepository->countByFederation();
        $membreGenre = $membreRepository->countByGenre();
        $total = $cotisationRepository->countByAmount(null)[0]["total"];

        $membreLabels = [];
        $membreValues = [];

        $membreValuesChart2 = [];
        foreach ($membreGenre as $membre) {
            array_push($membreValuesChart2, $membre['frequence']);
        }

        foreach ($membreData as $membre) {
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
            'totalMontant' => is_null($total) ? 0 : $total,
            'licence' => $licence,
            'credits' => $credits
        ]);
    }
}
