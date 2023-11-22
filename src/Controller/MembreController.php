<?php

namespace App\Controller;

use App\Entity\Federation;
use App\Entity\Membre;
use App\Entity\User;
use App\Form\Type\ExcelUploadType;
use App\Form\Type\MembreType;
use App\Repository\MembreRepository;
use App\Repository\TagRepository;
use App\Service\MembreCardPrinter;
use ChunkReadFilter;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\ExcelMembreImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
//use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use PhpOffice\PhpSpreadsheet\IOFactory;
//use Twig\Cache\NullCache;

class MembreController extends AbstractController
{

    private function generateIdNumber()
    {
        $part01 = rand(3000, 9999);
        $part02 = rand(300000, 999999);
        return '' . $part01 . $part02;
    }

    #[Route('/membre/new', name: 'membre_new')]
    public function new(Request $request, ManagerRegistry $doctrine, TagRepository $tagRepository, MembreRepository $membreRepository): Response
    {

        $membre = new Membre();


        $user = $this->getUser();
        $organisation = $user->getOrganisation();

        $membres = $doctrine->getRepository(Membre::class)->findBy([
            'visible' => null, 'organisation' => $organisation
        ]);
        $_SERVER['organisation_x'] = $organisation->getId();
        $form = $this->createForm(MembreType::class, $membre);
        $excelForm = $this->createForm(ExcelUploadType::class, null, [
            'action' => $this->generateUrl('membre_new'),
        ]);


        $form->handleRequest($request);
        $excelForm->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $membre = $form->getData();
            $p_membre = $membreRepository->findOneBy(['telephone' => $membre->getTelephone(), 'organisation' => $organisation]);
            if (!is_null($p_membre)) {
                $this->addFlash("error", "Un utilisateur avec ses informations existe déjà dans la Base de données!");
                return $this->redirectToRoute('membre_new');
            }

            /**
             * Inscription dans un groupe
             */
            $tagGen = $tagRepository->findOneBy(['code' => 'GENERAL', 'organisation' => $organisation]);
            $membre->addTag($tagGen);
            $membre->setNoidentification($this->generateIdNumber());
            $membre->setDateadhesion(new \DateTimeImmutable());
            $file = null;
            if (!is_null($request->files->get("membre"))) {
                $file = $request->files->get("membre")["avatar"];
            }

            if (!is_null($file)) {
                $extension = $file->guessExtension();
                if (!$extension) {
                    // extension cannot be guessed
                    $extension = 'bin';
                }
                $filename = rand(1, 99999) . '.' . $extension;
                $file->move('../public/uploads', $filename);
                $filename = "uploads" . "/" . $filename;
                $membre->setAvatar($filename);
            }

            if (is_null($membre->getGenre())) {
                $membre->setGenre("Homme"); //Homme par défaut
            }
            $entityManager = $doctrine->getManager();
            //get user organisation
            $membre->setOrganisation($organisation);

            $entityManager->persist($membre);
            $entityManager->flush();
            $this->addFlash("notice", "Contact ajouté avec succès!");
            return $this->redirectToRoute('membre_new');
        }
        $toast = [];
        if ($excelForm->isSubmitted() && $excelForm->isValid()) {
            $excelFile = $excelForm->get("attachement")->getData();
            $tags = $excelForm->get("tags");
            $excelImporter = new ExcelMembreImporter($excelFile, $doctrine, $this->getUser(), $tags);

            try {
                $excelImporter->processData();
                $toast = [
                    "isError" => false,
                    "message" => "Les données ont été correctement importées"
                ];
            } catch (\Throwable $th) {
                $toast = [
                    "isError" => true,
                    "message" => "L'erreur suivante est survenue lors du chargement: " . $th->getMessage()
                ];
            }
        }

        return $this->renderForm('membre/index.html.twig', [
            'controller_name' => 'MembreController',
            'form' => $form,
            'excelform' => $excelForm,
            'membres' => $membres,
            'toast' => $toast
        ]);
    }

    #[Route('/membre/update/{id}', name: 'membre_update')]
    public function update(Request $request, ManagerRegistry $doctrine, TagRepository $tagRepository, int $id): Response
    {


        $membre = $doctrine->getRepository(Membre::class)->find($id);

        $nvoMembre = new Membre();
        $_SERVER['organisation_x'] = $membre->getOrganisation()->getId();
        $form = $this->createForm(MembreType::class, $nvoMembre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nvoMembre = $form->getData();
            $tags = $nvoMembre->getTags();
            if ($tags) {
                $membre->emptyTags();
                foreach ($tags as $tag) {
                    $membre->addTag($tag);
                }
            }

            $membre->setNom(is_null($nvoMembre->getNom()) ? $membre->getNom() : $nvoMembre->getNom());
            $membre->setPostnom(is_null($nvoMembre->getPostnom()) ? $membre->getPostnom() : $nvoMembre->getPostnom());
            $membre->setPrenom(is_null($nvoMembre->getPrenom()) ? $membre->getPrenom() : $nvoMembre->getPrenom());
            $membre->setTelephone(is_null($nvoMembre->getTelephone()) ? $membre->getTelephone() : $nvoMembre->getTelephone());
            $membre->setGenre(is_null($nvoMembre->getGenre()) ? $membre->getGenre() : $nvoMembre->getGenre());
            if (!is_null($nvoMembre->getDatenaissance())) {
                $membre->setDatenaissance($nvoMembre->getDatenaissance());
            }
            $membre->setAdresse(is_null($nvoMembre->getAdresse()) ? $membre->getAdresse() : $nvoMembre->getAdresse());
            $membre->setQualite($nvoMembre->getQualite());

            $file = null;
            if (!is_null($request->files->get("membre"))) {
                $file = $request->files->get("membre")["avatar"];
            }

            if (!is_null($file) && !is_null($membre->getAvatar())) {
                try {
                    unlink('../public/' . $membre->getAvatar()); // supprime l'ancienne photo
                } catch (\Throwable $th) {
                    //file already deleted
                }

                $extension = $file->guessExtension();
                if (!$extension) {
                    // extension cannot be guessed
                    $extension = 'bin';
                }
                $filename = rand(1, 99999) . '.' . $extension;
                $file->move('../public/uploads', $filename);
                $filename = "uploads" . "/" . $filename;
                $membre->setAvatar($filename);
            }
            if (!is_null($nvoMembre->getFederation())) {
                $membre->setFederation($nvoMembre->getFederation());
            }


            $entityManager = $doctrine->getManager();
            $entityManager->persist($membre);
            $entityManager->flush();
        }
        return $this->redirectToRoute('membre_new');
    }

    

    #[Route('/membre/delete/{id}', name: 'membre_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, TagRepository $tagRepository, int $id): Response
    {
        $membre = $doctrine->getRepository(Membre::class)->find($id);
        if (is_null($membre)) {
            $this->addFlash("error", "Le membre n'existe pas!");
        }
        $membre->setVisible(false);
        $manager = $doctrine->getManager();
        $manager->remove($membre);
        $manager->flush();
        return $this->redirectToRoute('membre_new');
    }

    #[Route('/membre/print', name: 'membre_print', methods: ["POST"])]
    public function printCard(Request $request, ManagerRegistry $doctrine, MembreRepository $membreRepository): Response
    {
        $toast = ["isError" => false, "message" => ""];
        //$response->setData(['request' => json_encode($request)]);
        /*try {*/
        $idenfications = array_values($request->request->all());
        $membres = $membreRepository->findBy([
            'noidentification' => $idenfications
        ]);

        if (empty($membres)) {
            $toast["isError"] = true;
            $toast["message"] = "Aucun des numeros ne correspondent à des membres existants";
        }

        $html = $this->renderView("carte.html.twig", [
            "membres" => $membres
        ]);

        //dd($html);

        $printerService = new MembreCardPrinter();
        $response = $printerService->print($html);
        $filename = 'Exports cartes-' . rand(10000, 99999) . '.pdf';
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
