<?php

namespace App\Controller;

use App\Entity\Diffusion;
use App\Entity\Membre;
use App\Entity\ReferenceData;
use App\Form\Type\DiffusionType;
use App\Repository\DiffusionRepository;
use App\Repository\MembreRepository;
use App\Repository\ReferenceDataRepository;
use App\Repository\TagRepository;
use App\Service\MessageService;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiffusionController extends AbstractController
{
    #[Route('/diffusion', name: 'diffusion')]
    public function index(
        Request $request,
        ManagerRegistry $doctrine,
        DiffusionRepository $diffusionRepository,
        MembreRepository $membreRepository,
        TagRepository $tagRepository
    ): Response {

        $diffusion = new Diffusion();
        $user = $this->getUser();
        $organisation = $user->getOrganisation();
        $_SERVER['organisation_x'] = $organisation->getId();
        
        $form = $this->createForm(DiffusionType::class, $diffusion);

        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {
            
            $diffusion = $form->getData();
            $raw_provinces = $diffusion->getProvinces();
            $provinces = [];
            foreach ($raw_provinces as $rp) {
                array_push($provinces, $rp->getNom());
            }
            $diffusion->setProvinces($provinces);
            $raw_federations = $diffusion->getfederations();
            $federations = [];
            foreach ($raw_federations as $rp) {
                array_push($federations, $rp->getNom());
            }
            $raw_tags = $diffusion->getTags();
            //$phones = [];
            $tags = [];
            
            foreach ($raw_tags as $tag) {
                if($tag->getCode() == 'GENERAL'){
                    $alltags = $tagRepository->findBy(['organisation' => $organisation]);
                    foreach ($alltags as $altag) {
                        array_push($tags, $altag->getId());
                    }
                    break;
                }else {
                    array_push($tags, $tag->getId());
                }
            }

            $diffusion_tags = [];
            foreach($raw_tags as $tag){
                array_push($diffusion_tags, $tag->getName());
            }
            /*
                foreach ($raw_tags as $tag) {
                    array_push($tags, $tag->getCode());
                    $members = $tag->getMembres();
                    foreach ($members as $member) {
                        if (is_null($member->getVisible())) {
                            array_push($phones, $member->getTelephone());
                        }
                    }
                }
            */

            //$cannaux = $diffusion->getCanal();
            $tag_list = implode(",", $tags);
            $result = $diffusionRepository->countMembers($tag_list)[0];
            $nPhones = intval($result['compte']);

            $message = $diffusion->getContent();
            $parts = intval(strlen($message) / 160) + 1;
            $cost = 1.5 * $parts * $nPhones;
            $currentSolde = $organisation->getCredits();
            
            if (is_null($currentSolde)) {
                $this->addFlash("error", "Solde de message insuffisant pour la diffusion!");
                return $this->redirectToRoute('diffusion');
            } else if (intval($currentSolde) < $cost) {
                $this->addFlash("error", "Solde de message insuffisant pour la diffusion!");
                return $this->redirectToRoute('diffusion');
            }
            //dd($tag_list);
            $this->send($diffusion, $tag_list);

            /*
            if (in_array("WHA", $cannaux)) {
                $message = $diffusion->getRichText();
                $service = new MessageService('no-token');
                $secret = $this->getParameter('app.vonagekey') . ':' . $this->getParameter('app.vonagesecret');
                $result = $service->sendOneWhatsappMesssage(str_replace('+', '', $phones[0]), $message, base64_encode($secret));
                if ($result['http_status'] <= 300) {
                    $this->addFlash("notice", "Les messages whatsapp ont été correctement transférée!");
                } else {
                    $this->addFlash("error", "Une erreur lors de l'envoie via whatsapp, réessayez plus tard!");
                }
            }
            if (is_null($diffusion->getContent())) {
                $diffusion->setContent("voir vontenu enrichie...");
            }
            */
            //$diffusion->setTags($tags);
            $diffusion->setTitre("No-Title");
            $diffusion->setTags($diffusion_tags);
            $diffusion->setFederations($federations);
            $diffusion->setVisible(true);
            $diffusion->setOrganisation($organisation);
            $diffusion->setNumberOfMembers($nPhones);
            $organisation->setCredits($currentSolde - $cost);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($diffusion);
            $entityManager->persist($organisation);
            $entityManager->flush();

            return $this->redirectToRoute('diffusion');
        }

        return $this->renderForm('diffusion/index.html.twig', [
            'controller_name' => 'DiffusionController',
            'form' => $form,
            'membreCount' => $membreRepository->count(['visible' => null]),
            'diffusions' => $diffusionRepository->findBy(['visible' => true, 'organisation' => $organisation],['id' => 'DESC']),
            'credits' => 0
        ]);
    }

    #[Route('/diffusion/delete/{id}', name: 'diffusion_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, DiffusionRepository $diffusionRepository, $id): Response
    {

        $diffusion = $diffusionRepository->find($id);
        if (!is_null($diffusion)) {
            $diffusion->setVisible(false);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diffusion);
            $entityManager->flush();
        }


        return $this->redirectToRoute('diffusion');
    }

    private function is_windows()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        } else {
            return false;
        }
    }

    private function send(Diffusion $diffusion, string $tag_list)
    {

        $message = $diffusion->getContent();
        $token = $this->getParameter('app.smstoken');
        try {
            $script_path = dirname(getcwd()) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR;
            $lang = "python ";
            if(!$this->is_windows()){
                $lang = "python3 ";
            }
            $last = "";
            if(!is_null($diffusion->getSendername())){
                $sender = $diffusion->getSendername()->getSenderid();
                $last = " --sender \"$sender\" "; 
            }
            
            $command = $lang . $script_path . "bulksms.py --auth $token --message \"$message\" --group $tag_list" . $last;
            //dd($command);
            exec($command);
        } catch (\Throwable $th) {
            $this->addFlash("error", "Une erreur lors de la transmissions, réessayez plus tard!");
        }
    }
}
