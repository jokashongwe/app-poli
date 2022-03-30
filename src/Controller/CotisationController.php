<?php

namespace App\Controller;


use App\Entity\Cotisation;
use App\Entity\Membre;
use App\Form\Type\CotisationTye;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CotisationController extends AbstractController
{
    #[Route('/cotisation/{id}', name: 'cotisation_show')]
    public function index(Request $request, ManagerRegistry $managerRegistry, $id): Response
    {
        $membre = $managerRegistry->getRepository(Membre::class)->findOneBy([
            "id" => $id
        ]);

        if(is_null($membre)){
            return $this->redirectToRoute('dashboard');
        }

        $toast = null;

        if(!empty($request->query->all())){
            $ecode = $request->query->get("ecode");
            if(!is_null($ecode) && $ecode == "401"){
                $this->addFlash("notice", "Mauvais format de nombre");
            }
            if(!is_null($ecode) && $ecode == "404"){
                $this->addFlash("notice", "Requete invalide");
            }
        }

        $form = $this->createForm(CotisationTye::class, null, [
            'action' => $this->generateUrl('cotisation_new', ["id" => $id]),
        ]);

        return $this->renderForm('cotisation/index.html.twig', [
            'membre' => $membre,
            'toast' => $toast,
            'form' => $form
        ]);
    }
    #[Route('/cotisation/new/{id}', name: 'cotisation_new', methods: ["POST"])]
    public function new(Request $request, ManagerRegistry $managerRegistry, $id){
        $montant = $request->request->all()["cotisation_tye"]["montant"];
        $ecode = 201;
        try {
            $montantNum = floatval($montant);
            $membre = $managerRegistry->getRepository(Membre::class)->findOneBy([
                "id" => $id
            ]);

            if( is_null($membre) ){
                $ecode = 404;
                return $this->redirectToRoute("cotisation_show", ["id" => $id, "ecode" => $ecode]);
            }

            $cotisation = new Cotisation();
            $cotisation->setMontant($montant);
            $cotisation->setMembre($membre);
            $cotisation->setDatepaiement(new \DateTimeImmutable());

            $manager = $managerRegistry->getManager();
            $manager->persist($cotisation);
            $manager->flush();

        }catch (\Throwable $th) {
            $ecode = 401;
        }
        return $this->redirectToRoute("cotisation_show", ["id" => $id, "ecode" => $ecode]);
    }
}
