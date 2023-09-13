<?php

namespace App\Controller;

use App\Entity\Diffusion;
use App\Entity\Membre;
use App\Form\Type\DiffusionType;
use App\Repository\DiffusionRepository;
use App\Repository\MembreRepository;
use App\Service\MessageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiffusionController extends AbstractController
{
    #[Route('/diffusion', name: 'diffusion')]
    public function index(Request $request, ManagerRegistry $doctrine, DiffusionRepository $diffusionRepository, MembreRepository $membreRepository): Response
    {

        $diffusion = new Diffusion();

        $form = $this->createForm(DiffusionType::class, $diffusion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $diffusion = $form->getData();
            $raw_provinces = $diffusion->getProvinces();
            $provinces = [];
            foreach ($raw_provinces as $rp) {
                array_push($provinces, $rp->getNom());
            }
            $diffusion->setProvinces($provinces);
            $raw_federations = $diffusion->getfederations();
            $federations = [];
            foreach ($raw_federations as $rp) {
                array_push($federations, $rp->getNom());
            }
            $diffusion->setFederations($federations);
            $diffusion->setVisible(true);
            $nPhones = 0;
            $nPhones = $this->send($diffusion, $membreRepository);
            $diffusion->setNumberOfMembers($nPhones);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diffusion);
            $entityManager->flush();


            return $this->redirectToRoute('diffusion');
        }


        return $this->renderForm('diffusion/index.html.twig', [
            'controller_name' => 'DiffusionController',
            'form' => $form,
            'diffusions' => $diffusionRepository->findBy(['visible' => true])
        ]);
    }

    #[Route('/diffusion/delete/{id}', name: 'diffusion_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, DiffusionRepository $diffusionRepository, $id): Response
    {

        $diffusion = $diffusionRepository->find($id);
        if (!is_null($diffusion)) {
            $diffusion->setVisible(false);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diffusion);
            $entityManager->flush();
        }


        return $this->redirectToRoute('diffusion');
    }

    private function send(Diffusion $diffusion, MembreRepository $membreRepository)
    {

        $membres = $membreRepository->findByDiffusion($diffusion->getProvinces(), $diffusion->getFederations());
        $phones = [];
        foreach ($membres as $membre) {
            $phone = $membre->getTelephone();
            if (!is_null($phone) && !empty($phone) && !in_array($phone, $phones)) {
                array_push($phones, $phone);
            }
        }

        //dd($phones);

        $message = $diffusion->getTitre() . '  ' . $diffusion->getContent();
        //dd($message);

        
        $msgService = new MessageService($this->getParameter('app.bulksmstoken'));

        try {
            if (!empty($phones)) {
                $result = $msgService->sendManySMS($message, $phones);
                dd($result);
                if ($result['http_status'] != 201) {
                    $this->addFlash("notice", "Les messages ont été correctement transférée!");
                } else {
                    $this->addFlash("notice", "Une erreur lors de la transmissions, réessayez plus tard!");
                }
            } else {
                $this->addFlash("notice", "Aucun membres trouvé pour la diffusion!");
            }
        } catch (\Throwable $th) {
            $this->addFlash("notice", "Une erreur lors de la transmissions, réessayez plus tard!");
        }
        
        return sizeof($phones);
    }
}
