<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    private $copyrightText = "© 2023";

    #[Route('/', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {

        //Recuperer les erreurs de login, s'il y en a 
        $error = $authenticationUtils->getLastAuthenticationError();
        $message = null;
        if(!is_null($error)){
            $message = $error->getMessage();
            if(strpos($message, "credentials") >= 0){
                $message = "Identifiants invalides";
            }
        }
        
      
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username'   => $lastUsername,
            'error'           => $message,
            'copyrightText'   => $this->copyrightText  
        ]);
    }

    #[Route('/logout', name:'logout', methods:["GET"])]
    public function logout(): void
    {
        //DO NOTHING
    }
}
