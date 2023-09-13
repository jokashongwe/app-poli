<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
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

    #[Route('/account/deactivate/{id}', name: 'user_deactivate')]
    public function deactivate(Request $request, ManagerRegistry $registry, $id): Response
    {
        $user = $registry->getRepository(User::class)->find($id);
        $user->setActive(false);
        $manager = $registry->getManager();
        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute('user_show');
    }


    #[Route('/account/new', name: 'user_new')]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $registry){
        $user = new user();

        if(empty($request->request->all()) || !array_key_exists("user", $request->request->all())){
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
