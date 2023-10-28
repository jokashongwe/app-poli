<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\OrganisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }

    #[Route('/register/validate', name: 'app_register_validate')]
    public function validate(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        ManagerRegistry $managerRegistry
    ): Response {
        $username = $request->get('_username');
        $password = $request->get('_password');
        $confirm_password = $request->get('_password_confirm');
        if (is_null($username) || is_null($password) || is_null($confirm_password) || ($password != $confirm_password)) {
            $this->addFlash("error", "Les informations transmises ne sont pas valides!");
            return $this->redirectToRoute('app_register');
        }
        try {
            $user = $userRepository->findOneBy(['username' => $username]);
            if(!is_null($user)){
                $this->addFlash("error", "Un utilisateur existe déjà avec cette adresse email");
                return $this->redirectToRoute('app_register');
            }
            //create the user
            $organisation = new Organisation();
            $organisation->setName(explode('@', $username)[0]);
            $organisation->setCredits(50.00);
            $manager = $managerRegistry->getManager();
            $manager->persist($organisation);
            //create the default tag
            $tag = new Tag();
            $tag->setName("Général");
            $tag->setCode("GENERAL");
            $tag->setOrganisation($organisation);
            $manager->persist($tag);
            //
            $user = new User();
            $user->setUsername($username);
            $user->setActive(true);
            $user->setDatecreation(new \DateTimeImmutable());
            $user->setNom(explode('@', $username)[0]);
            $user->setOrganisation($organisation);
            $user->setRoles(["ROLE_ADMIN", "SUPER-ADMIN", "ROLE_USER", "ROLE_MEMBRE", "ROLE_DIFFUSION"]);
            $user->setPrenom("No");
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
            $manager->flush();
            return $this->render('register/confirm.html.twig', [
                'controller_name' => 'RegisterController',
            ]);
        } catch (\Throwable $th) {
            dd($th);
            $this->addFlash("error", $th->getMessage());
            return $this->redirectToRoute('app_register');
        }
    }
}
