<?php

namespace App\Controller;

use App\Entity\Federation;
use App\Entity\Membre;
use App\Form\Type\ExcelUploadType;
use App\Form\Type\MembreType;
use App\Service\MembreCardPrinter;
use ChunkReadFilter;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\ExcelMembreImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Twig\Cache\NullCache;

class MembreController extends AbstractController
{

    private function generateIdNumber()
    {
        $part01 = rand(3000, 9999);
        $part02 = rand(300000, 999999);
        $part03 = date("Y");
        return '' . $part01 . '/' . $part02 . '/' . $part03;
    }

    #[Route('/membre/new', name: 'membre_new')]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {

        $membre = new Membre();

        $membres = $doctrine->getRepository(Membre::class)->findAll();

        $form = $this->createForm(MembreType::class, $membre);
        $excelForm = $this->createForm(ExcelUploadType::class, null, [
            'action' => $this->generateUrl('membre_new'),
        ]);


        $form->handleRequest($request);
        $excelForm->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $membre = $form->getData();
            $membre->setNoidentification($this->generateIdNumber());
            $membre->setDateadhesion(new \DateTimeImmutable());
            $file = $request->files->get("membre")["avatar"];
            $extension = $file->guessExtension();
            if (!$extension) {
                // extension cannot be guessed
                $extension = 'bin';
            }
            $filename = rand(1, 99999) . '.' . $extension;
            $file->move('../public/uploads', $filename);
            $filename = "uploads" . "/" . $filename;
            $membre->setAvatar($filename);

            if (is_null($membre->getGenre())) {
                $membre->setGenre("Homme");
            }
            $entityManager = $doctrine->getManager();

            $entityManager->persist($membre);
            $entityManager->flush();


            return $this->redirectToRoute('membre_new');
        }
        $toast = [];
        if ($excelForm->isSubmitted() && $excelForm->isValid()) {
            $excelFile = $excelForm->get("attachement")->getData();
            $excelImporter = new ExcelMembreImporter($excelFile, $doctrine);

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
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {


        $membre = $doctrine->getRepository(Membre::class)->find($id);

        $nvoMembre = new Membre();

        $form = $this->createForm(MembreType::class, $nvoMembre);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $nvoMembre = $form->getData();

            $membre->setNom($nvoMembre->getNom());
            $membre->setPostnom($nvoMembre->getPostnom());
            $membre->setPrenom($nvoMembre->getPrenom());
            $membre->setTelephone($nvoMembre->getTelephone());
            $membre->setGenre($nvoMembre->getGenre());
            $membre->setDatenaissance($nvoMembre->getDatenaissance());
            $membre->setAdresse($nvoMembre->getAdresse());
            $membre->setQualite($nvoMembre->getQualite());

            $file = $request->files->get("membre")["avatar"];

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
            $membre->setFederation($nvoMembre->getFederation());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($membre);
            $entityManager->flush();
        }
        return $this->redirectToRoute('membre_new');
    }

    #[Route('/membre/print', name: 'membre_print', methods: ["POST"])]
    public function printCard(Request $request, ManagerRegistry $doctrine): Response
    {
        $toast = ["isError" => false, "message" => ""];
        //$response->setData(['request' => json_encode($request)]);
        /*try {*/
            $idenfications = array_values($request->request->all());
            $membres = $doctrine->getRepository(Membre::class)->findBy([
                'noidentification' => $idenfications
            ]);

            if (is_null($membres)) {
                $toast["isError"] = true;
                $toast["message"] = "Aucun des numeros ne correspondent à des membres existants";
            }

            $html = $this->renderView("carte.html.twig", [
                "membres" => $membres
            ]);

            $printerService = new MembreCardPrinter();
            $printerService->print($html);

            $toast["message"] = "Le téléchargement vas débuter sous peu";

        /*} catch (\Throwable $th) {
            $toast["isError"] = true;
            $toast["message"] = "L'erreur suivante est survenue: " . $th->getMessage();
        }*/
        return $this->redirectToRoute('membre_new', ["toast" => $toast]);

    }
}
