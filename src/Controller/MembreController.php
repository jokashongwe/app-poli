<?php

namespace App\Controller;

use App\Entity\Federation;
use App\Entity\Membre;
use App\Form\Type\MembreType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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

        $form->handleRequest($request);


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
            $filename = rand(1, 99999).'.'. $extension;
            $file->move('../public/uploads', $filename);
            $filename = "uploads" . "/". $filename;
            $membre->setAvatar($filename);

            if(is_null($membre->getGenre())){
                $membre->setGenre("Homme");
            }
            $entityManager = $doctrine->getManager();

            $entityManager->persist($membre);
            $entityManager->flush();


            return $this->redirectToRoute('membre_new');
        }


        return $this->renderForm('membre/index.html.twig', [
            'controller_name' => 'MembreController',
            'form'          => $form,
            'membres' => $membres
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
            
            $file = $request->files->get("membre")["avatar"];
            
            if(!is_null($file) && !is_null( $membre->getAvatar() ) ){
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
                $filename = rand(1, 99999).'.'. $extension;
                $file->move('../public/uploads', $filename);
                $filename = "uploads" . "/". $filename;
                $membre->setAvatar($filename);
            }
            $membre->setFederation($nvoMembre->getFederation());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($membre);
            $entityManager->flush();


            return $this->redirectToRoute('membre_new');
        }
    }
}
