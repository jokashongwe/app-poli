<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\Setting;
use App\Form\Type\MembreType;
use App\Repository\SettingRepository;
use App\Repository\TagRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdhesionController extends AbstractController
{
    private function generateIdNumber()
    {
        $part01 = rand(3000, 9999);
        $part02 = rand(300000, 999999);
        $part03 = date("Y");
        return '' . $part01 . '/' . $part02 . '/' . $part03;
    }

    #[Route('/enregistrement', name: 'app_adhesion')]
    public function index(Request $request, ManagerRegistry $doctrine,TagRepository $tagRepository, SettingRepository $settingRepository): Response
    {
        $membre = new Membre();
        $form = $this->createForm(MembreType::class, $membre);

        $setting = $settingRepository->findAll();
        if(!empty($setting)){
            $setting = $setting[0];
        }
        
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $membre = $form->getData();
            $membre->setNoidentification($this->generateIdNumber());
            $membre->setDateadhesion(new \DateTimeImmutable());
            $file = $request->files->get("membre")["avatar"];
            $extension = $file->guessExtension();
            if (!$extension) {
                $extension = 'bin';
            }
            $filename = rand(1, 99999) . '.' . $extension;
            $file->move('../public/uploads', $filename);
            $filename = "uploads" . "/" . $filename;
            $membre->setAvatar($filename);
            $tagGen = $tagRepository->findOneBy(['code' => 'GENERAL']);
            if(!is_null($tagGen)){
                $membre->addTag($tagGen);
            }
            $nom = $membre->getFederation()->getNom();
            $fedTag = $tagRepository->findOneBy(['name' => $nom]); // ajout dans le groupe de la fédération
            if (!is_null($fedTag)) {
                $membre->addTag($fedTag);
            }
            

            if (is_null($membre->getGenre())) {
                $membre->setGenre("Homme");
            }
            $entityManager = $doctrine->getManager();

            $entityManager->persist($membre);
            $entityManager->flush();


            return $this->redirectToRoute('app_adhesion_done');
        }
        
        return $this->render('adhesion/index.html.twig', [
            'controller_name' => 'AdhesionController',
            'form' => $form->createView(),
            'setting' => $setting
        ]);
    }

    #[Route('/adhesion', name: 'app_adhesion_done')]
    public function onsuccess(Request $request, ManagerRegistry $doctrine, SettingRepository $settingRepository): Response
    {
        $setting = $settingRepository->findAll();
        if(!empty($setting)){
            $setting = $setting[0];
        }
        
        return $this->render('adhesion/success.html.twig', [
            'controller_name' => 'AdhesionController',
            'setting' => $setting
        ]);
    }
    
}
