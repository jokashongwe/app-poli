<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    private $copyrightText = "© 2022 - Action des Alliés pour le Développement Social (AADS)";

    #[Route('/', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {

        //Recuperer les erreurs de login, s'il y en a 
        $error = $authenticationUtils->getLastAuthenticationError();
      
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username'   => $lastUsername,
            'error'           => $error,
            'copyrightText'   => $this->copyrightText  
        ]);
    }

    #[Route('/logout', name:'logout', methods:["GET"])]
    public function logout(): void
    {
        //DO NOTHING
    }
}
