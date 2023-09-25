<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\CandidatRepository;
use App\Repository\MembreRepository;
use App\Repository\TemoinRepository;
use App\Service\MessageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/account', name: 'user_show')]
    public function index(Request $request, ManagerRegistry $registry): Response
    {
        $users = $registry->getRepository(User::class)->findAll();
        $user = new user();
        $form = $this->createForm(UserType::class, $user, [
            "action" => $this->generateUrl("user_new")
        ]);

        return $this->renderForm('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'form' => $form
        ]);
    }

    #[Route('/account/update/{id}', name: 'user_update')]
    public function update(Request $request, ManagerRegistry $registry, $id): Response
    {
        $users = $registry->getRepository(User::class)->findAll();
        $user = new user();
        $form = $this->createForm(UserType::class, $user, [
            "action" => $this->generateUrl("user_new")
        ]);

        return $this->renderForm('user/index.html.twig', [
            'users' => $users,
            'form' => $form
        ]);
    }

    #[Route('/account/newpin/{id}', name: 'user_newpin')]
    public function newpin(Request $request, ManagerRegistry $registry, CandidatRepository $candidatRepository, TemoinRepository $temoinRepository,UserPasswordHasherInterface $passwordHasher, $id): Response
    {
        $user = $registry->getRepository(User::class)->find($id);
        $obj = $temoinRepository->findOneBy(['user' => $user]);
        if(is_null($obj)){
            $obj  = $candidatRepository->findOneBy(['user' => $user]);
            if(is_null($obj)){
                $this->addFlash("notice", "Cet utilisateur n'est attaché ni à un candidat ni à un témoin");
                return $this->redirectToRoute('user_show');
            }
        }
        $membre = $obj->getMembre();
        $service = new MessageService($this->getParameter('app.bulksmstoken'));
        $newPIN = '' . rand(300000, 999999);
        $message = 'Bonjour, votre nouveau PIN est le suivant: ' . $newPIN;
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $newPIN
        );
        $user->setPassword($hashedPassword);
        $manager = $registry->getManager();
        $manager->persist($user);
        $service->sendManySMS(
            $message,
            [$membre->getTelephone()],
            $this->getParameter('app.senderid'),
            $this->getParameter('app.sendermode')
        );
        $manager->flush();
        return $this->redirectToRoute('user_show');
    }

    #[Route('/account/deactivate/{id}', name: 'user_deactivate')]
    public function deactivate(Request $request, ManagerRegistry $registry, $id): Response
    {
        $user = $registry->getRepository(User::class)->find($id);
        $user->setActive(!$user->getActive());
        $manager = $registry->getManager();
        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute('user_show');
    }


    #[Route('/account/new', name: 'user_new')]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $registry)
    {
        $user = new user();

        if (empty($request->request->all()) || !array_key_exists("user", $request->request->all())) {
            return $this->redirectToRoute("user_show");
        }

        $userAr = $request->request->all()["user"];

        $user->setUsername($userAr["username"]);
        $user->setNom($userAr["nom"]);
        $user->setPostnom($userAr["postnom"]);
        $user->setPrenom($userAr["prenom"]);
        $user->setRoles($userAr["roles"]);
        $user->setDatecreation(new \DateTimeImmutable());

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $userAr["password"]
        );
        $user->setPassword($hashedPassword);

        $manager = $registry->getManager();
        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute("user_show");
    }
}
