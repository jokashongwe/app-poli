<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\Type\DiffusionType;
use App\Service\MessageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiffusionController extends AbstractController
{
    #[Route('/diffusion', name: 'diffusion')]
    public function index(): Response
    {

        $form = $this->createForm(DiffusionType::class, null,[
            'action'=> $this->generateUrl("diffusion_send")
        ]);



        return $this->renderForm('diffusion/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/diffusion/send', name: 'diffusion_send', methods: ["POST"])]
    public  function send(Request $request, ManagerRegistry $registry){
        $diffusion = $request->request->all()["diffusion"];
        $membres = $registry->getRepository(Membre::class)->findByDiffusion($diffusion["province"], $diffusion["federation"]);
        $phones = [];
        foreach ($membres as $membre){
            $phone = $membre->getTelephone();
            if(!is_null($phone) && !empty($phone) && !in_array($phone, $phones)){
                array_push($phones, $phone);
            }
        }


        $msgService = new MessageService("SMS", false);

        try {
            if(!empty($phones)){
                $status = $msgService->sendManySMS($diffusion["titre"] . '-' . $diffusion["contenu"], "AADS", $phones);

                if($status) {
                    $this->addFlash("notice", "Les messages ont été correctement transférée!");
                }else {
                    $this->addFlash("notice", "Une erreur lors de la transmissions, réessayez plus tard!");
                }
            }else {
                $this->addFlash("notice", "Une erreur lors de la transmissions, réessayez plus tard!");
            }
        }catch (\Throwable $th){
            $this->addFlash("notice", "Une erreur lors de la transmissions, réessayez plus tard!");
        }
        return $this->redirectToRoute("diffusion");
    }
}
