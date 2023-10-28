<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Federation;
use App\Entity\Membre;
use App\Entity\ReferenceData;
use App\Repository\CotisationRepository;
use App\Repository\DiffusionRepository;
use App\Repository\MembreRepository;
use App\Repository\ReferenceDataRepository;
use App\Service\MessageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private function compter(ManagerRegistry $em, $tableClass)
    {
        return $em->getRepository($tableClass)->compter();
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(
        ManagerRegistry $entityManager,
        MembreRepository $membreRepository,
        CotisationRepository $cotisationRepository,
        ReferenceDataRepository $referenceDataRepository,
        DiffusionRepository $diffusionRepository,
    ): Response {
        $license = $this->getParameter('app.systemlicence');
        if ($license != 'MARKETING') {
            $user = $this->getUser();
            if (!is_null($user) && !$user->getActive()) {
                return $this->redirectToRoute("logout");
            }
            //$service = new MessageService($this->getParameter('app.bulksmstoken'));
            //$result = $service->getCredits();
            ///$response = json_decode($result['server_response'], true);
            
            $credits = 0;
            $organisation = $user->getOrganisation();
            $credits = $organisation->getCredits();
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
                'licence' => $license,
                'credits' => $credits
            ]);
        }
        return $this->redirectToRoute('diffusion');
    }

    private function is_windows()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        } else {
            return false;
        }
    }

    #[Route('/dashboard/send_message', name: 'dashboard_send')]
    public function send_message(Request $request, ManagerRegistry $managerRegistry)
    {
        $message = $request->get("message");
        $phone = $request->get("phone");
        $this->send($message, $phone);
        $user = $this->getUser();
        $organisation = $user->getOrganisation();
        $current = $organisation->getCredits();

        $parts = intval(strlen($message) / 156) + 1;
        $cost = 1.5 * $parts;
        if($current < $cost){
            $this->addFlash('error', 'solde insuffisant');
            return $this->redirectToRoute('dashboard');
        }
        $organisation->setCredits($current - $cost);
        $manager = $managerRegistry->getManager();
        $manager->persist($organisation);
        $manager->flush();

        return $this->redirectToRoute('dashboard');
    }

    private function send($message, $phone)
    {

        $token = $this->getParameter('app.bulksmstoken');
        try {
            $script_path = dirname(getcwd()) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR;
            $lang = "python3 ";
            if($this->is_windows()){
                $lang = "python ";
            }
            $command = $lang . $script_path . "orangesms.py --auth $token --message \"$message\" --phone $phone";
            //dd($command);
            exec($command);
        } catch (\Throwable $th) {
            $this->addFlash("error", "Une erreur lors de la transmissions, réessayez plus tard!");
        }
    }
}

