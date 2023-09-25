<?php

namespace App\Controller;

use App\Entity\Diffusion;
use App\Entity\Membre;
use App\Form\Type\DiffusionType;
use App\Repository\DiffusionRepository;
use App\Repository\MembreRepository;
use App\Repository\ReferenceDataRepository;
use App\Service\MessageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiffusionController extends AbstractController
{
    #[Route('/diffusion', name: 'diffusion')]
    public function index(
        Request $request,
        ManagerRegistry $doctrine,
        DiffusionRepository $diffusionRepository,
        MembreRepository $membreRepository,
        ReferenceDataRepository $referenceDataRepository
    ): Response {

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
            $raw_tags = $diffusion->getTags();
            $phones = [];
            $tags = [];
            foreach ($raw_tags as $tag) {
                array_push($tags, $tag->getCode());
                $members = $tag->getMembres();
                foreach ($members as $member) {
                    if (is_null($member->getVisible())) {
                        array_push($phones, $member->getTelephone());
                    }
                }
            }
            $message = $diffusion->getContent();
            $parts = intval(strlen($message) / 153) + 1;
            $count = sizeof($phones);
            $cost = 1.5 * $parts * $count;
            $currentSolde = $referenceDataRepository->findOneBy(['code' => 'CREDITS']);
            if (is_null($currentSolde)) {
                $this->addFlash("error", "Solde de message insuffisant pour la diffusion!");
                return $this->redirectToRoute('diffusion');
            } else if (intval($currentSolde->getValue()) < $cost){
                $this->addFlash("error", "Solde de message insuffisant pour la diffusion!");
                return $this->redirectToRoute('diffusion');
            }
            $diffusion->setTags($tags);
            $diffusion->setTitre("No-Title");
            $diffusion->setFederations($federations);
            $diffusion->setVisible(true);
            $nPhones = 0;
            $nPhones = $this->send($phones, $diffusion);
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

    private function send($phones, Diffusion $diffusion)
    {


        $message = $diffusion->getContent();

        $msgService = new MessageService($this->getParameter('app.bulksmstoken'));

        try {
            if (!empty($phones)) {
                $result = $msgService->sendManySMS(
                    $message,
                    $phones,
                    $this->getParameter('app.senderid'),
                    $this->getParameter('app.sendermode')
                );
                if ($result['http_status'] <= 201) {
                    $this->addFlash("notice", "Les messages ont été correctement transférée!");
                } else {
                    $this->addFlash("error", "Une erreur lors de la transmissions, réessayez plus tard!");
                }
            }
        } catch (\Throwable $th) {
            $this->addFlash("error", "Une erreur lors de la transmissions, réessayez plus tard!");
        }

        return sizeof($phones);
    }
}
