<?php

namespace App\Controller;

use App\Entity\Temoin;
use App\Entity\User;
use App\Form\Type\TemoinType;
use App\Repository\SettingRepository;
use App\Repository\TemoinRepository;
use App\Repository\UserRepository;
use App\Service\MessageService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TemoinController extends AbstractController
{
    #[Route('/temoin', name: 'app_temoin')]
    public function index(Request $request, ManagerRegistry $doctrine, 
        TemoinRepository $temoinRepository,UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        SettingRepository $settingRepository): Response
    {
        $temoin = new Temoin();

        $form = $this->createForm(TemoinType::class, $temoin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $setting = $settingRepository->findAll();
            if(!empty($setting)){
                $setting = $setting[0];
            }

            $temoin = $form->getData();
            //dd($temoin);
            /**
             * création du User
             * Notification par SMS
             */
            $membre = $temoin->getMembre();
            $telephone = $membre->getTelephone();
        
            $entityManager = $doctrine->getManager();
            $code =  rand(300000, 999999);
            $temoin->setBackupCode($code);
            $message = "Bonjour, vous etes desormais temoin dans le regroupement ". $setting->getSigle() . ", PIN: " . $code;
            $msgService = new MessageService($this->getParameter('app.bulksmstoken'));
            $result = $msgService->sendManySMS(
                $message,
                [$telephone],
                $this->getParameter('app.senderid'),
                $this->getParameter('app.sendermode')
            );
            $telephone = str_replace('+243', '0', $telephone);
            if ($result['http_status'] == 201) {
                $temoinUser = $userRepository->findOneBy(['username' => $telephone]);
                if( empty($temoinUser) ){
                    $temoinUser = new User();
                }
                $temoinUser->setUsername(str_replace("+243", "0", $telephone));
                $temoinUser->setNom($membre->getNom());
                $temoinUser->setPostnom($membre->getPostnom());
                $temoinUser->setPrenom($membre->getPrenom());
                $temoinUser->setDatecreation(new DateTime());
                $temoinUser->setActive(true);
                $temoinUser->setVisible(true);
                $temoinUser->setRoles(['ROLE_TEMOIN']);
                $temoinUser->setPassword($userPasswordHasherInterface->hashPassword($temoinUser, $code));
                $entityManager->persist($temoinUser);
                $temoin->setUser($temoinUser);
                $entityManager->persist($temoin);
                $entityManager->flush();
                
                return $this->redirectToRoute('app_temoin');
            }else {
                $this->addFlash("notice", "Impossible de contacter le serveur de Messsagerie");
            } 
            
            return $this->redirectToRoute('app_temoin');
        }


        return $this->renderForm('temoin/index.html.twig', [
            'controller_name' => 'TemoinController',
            'form' => $form,
            'temoins' => $temoinRepository->findAll()
        ]);
    }

    #[Route('/temoin/delete/{id}', name: 'app_temoin_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, TemoinRepository $temoinRepository, $id)
    {
        $temoin = $temoinRepository->find($id);
        if (is_null($temoin)) {
            return $this->redirectToRoute('app_temoin');
        }
        $user = $temoin->getUser();
        $em = $doctrine->getManager();
        $em->remove($user);
        $em->remove($temoin);
        $em->flush();
        return $this->redirectToRoute('app_temoin');
    }

    #[Route('api/temoin/info', name: 'app_temoin_info')]
    public function get_info(Request $request, ManagerRegistry $doctrine, TemoinRepository $temoinRepository){
        
        $temoin = $temoinRepository->findOneBy(['user' => $this->getUser()]);
        //$result = $serializer->normalize($temoin, null, [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);
        return $this->json([
            'data' => $temoin->getSerialized(),
            'success' => true
        ]);
    }

    #[Route('api/temoin/{id}', name: 'app_temoin_upload')]
    public function upload_result(Request $request, ManagerRegistry $doctrine, TemoinRepository $temoinRepository, $id){
        dd($request);
    }

}   
