<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\Type\SettingType;
use App\Repository\SettingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingController extends AbstractController
{
    #[Route('/setting', name: 'app_setting')]
    public function index(Request $request, ManagerRegistry $doctrine, SettingRepository $settingRepository): Response
    {
        $setting = $settingRepository->findAll();
        if(empty($setting)){
            $setting = new Setting();
        }else {
            $setting = $setting[0];
        }
        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $setting = $form->getData();

            $photoPresident = $request->files->get("setting")["photoPresident"];
            $logo = $request->files->get("setting")["logo"];
            $extension = $photoPresident->guessExtension();
            $extensionLogo = $logo->guessExtension();
            if (!$extension) {
                // extension cannot be guessed
                $extension = 'bin';
            }
            if (!$extensionLogo) {
                // extension cannot be guessed
                $extensionLogo = 'bin';
            }
            $filename = rand(200000, 999990) . '.' . $extension;
            $filenameLogo = rand(200000, 999990) . '.' . $extensionLogo;
            $photoPresident->move('../public/uploads', $filename);
            $logo->move('../public/uploads', $filenameLogo);
            $filename = "uploads" . "/" . $filename;
            $filenameLogo = "uploads" . "/" . $filenameLogo;
            $setting->setPhotoPresident($filename);
            $setting->setLogo($filenameLogo);
            
            $entityManager = $doctrine->getManager();
            $entityManager->persist($setting);
            $entityManager->flush();
            
            $this->addFlash('success', 'Paramètres enregistré');

            return $this->redirectToRoute('app_setting');
        }


        return $this->render('setting/index.html.twig', [
            'controller_name' => 'SettingController',
            'form' => $form->createView()
        ]);
    }
}
