<?php

namespace App\Controller;

use App\Entity\Temoin;
use App\Entity\User;
use App\Form\Type\TemoinType;
use App\Repository\TemoinRepository;
use App\Service\MessageService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TemoinController extends AbstractController
{
    #[Route('/temoin', name: 'app_temoin')]
    public function index(Request $request, ManagerRegistry $doctrine, TemoinRepository $temoinRepository, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $temoin = new Temoin();

        $form = $this->createForm(TemoinType::class, $temoin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $temoin = $form->getData();
            /**
             * création du User
             * Notification par SMS
             */
            $membre = $temoin->getMembre();
            $telephone = $membre->getTelephone();
            /*
            $nomCandidat = null;
            $candidat = $temoin->getCandidat();
            if(!is_null($candidat)){
                $nomCandidat = '' . $candidat->getMembre();
            }
            */
            $entityManager = $doctrine->getManager();
            $code =  rand(300000, 999999);
            $temoin->setBackupCode($code);
            $message = "Bonjour, vous etes desormais temoin dans le regroupement XYZ, PIN: " . $code;
            $msgService = new MessageService($this->getParameter('app.bulksmstoken'));
            $result = $msgService->sendManySMS($message, [$telephone]);
            if ($result['http_status'] == 201) {
                $temoinUser = new User();
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
            }
            $this->addFlash("notice", "Impossible de contacter le serveur de Messsagerie");
            return $this->redirectToRoute('app_temoin');
        }


        return $this->renderForm('temoin/index.html.twig', [
            'controller_name' => 'TemoinController',
            'form' => $form,
            'temoins' => $temoinRepository->findAll()
        ]);
    }

    #[Route('/temoin/delete/{id}', name: 'app_temoin_delete')]
    public function delete(Request $request,ManagerRegistry $doctrine, TemoinRepository $temoinRepository, $id){
        $temoin = $temoinRepository->find($id);
        if(is_null($temoin)){
            return $this->redirectToRoute('app_temoin');
        }
        $user = $temoin->getUser();
        $em = $doctrine->getManager();
        $em->remove($user);
        $em->remove($temoin);
        $em->flush();
        return $this->redirectToRoute('app_temoin');
    }
}
