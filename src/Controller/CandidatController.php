<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\User;
use App\Form\Type\CandidatType;
use App\Repository\CandidatRepository;
use App\Repository\ResultatRepository;
use App\Repository\UserRepository;
use App\Service\MessageService;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CandidatController extends AbstractController
{
    #[Route('/candidat', name: 'app_candidat')]
    public function index(Request $request, ManagerRegistry $doctrine,ResultatRepository $resultatRepository, UserRepository $userRepository, CandidatRepository $candidatRepository, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $candidat = new Candidat();

        $form = $this->createForm(CandidatType::class, $candidat);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $candidat = $form->getData();
            /**
             * création du User
             * Notification par SMS
             */
            $membre = $candidat->getMembre();
            $telephone = $membre->getTelephone();
            $user = $userRepository->findOneBy(['username' => str_replace("+243", "0", $telephone)]);
            if (!is_null($user)) {
                $this->addFlash("notice", "Un utilisateur avec ce téléphone existe déjà");
                return $this->redirectToRoute('app_candidat');
            }
            /*
            $nomCandidat = null;
            $candidat = $temoin->getCandidat();
            if(!is_null($candidat)){
                $nomCandidat = '' . $candidat->getMembre();
            }
            */
            $entityManager = $doctrine->getManager();
            $code =  rand(300000, 999999);
            $candidat->setBackupCode($code);
            $message = "Bonjour, vous avez ete ajouter comme candidat sur la plateforme du regroupement XYZ, PIN: " . $code;
            $msgService = new MessageService($this->getParameter('app.bulksmstoken'));
            $result = $msgService->sendManySMS(
                $message,
                [$telephone],
                $this->getParameter('app.senderid'),
                $this->getParameter('app.sendermode')
            );
            if ($result['http_status'] == 201) {
                $candidatUser = new User();
                $candidatUser->setUsername(str_replace("+243", "0", $telephone));
                $candidatUser->setNom($membre->getNom());
                $candidatUser->setPostnom($membre->getPostnom());
                $candidatUser->setPrenom($membre->getPrenom());
                $candidatUser->setDatecreation(new DateTime());
                $candidatUser->setActive(true);
                $candidatUser->setVisible(true);
                $candidatUser->setRoles(['ROLE_CANDIDAT']);
                $candidatUser->setPassword($userPasswordHasherInterface->hashPassword($candidatUser, $code));
                $entityManager->persist($candidatUser);
                $candidat->setUser($candidatUser);
                $candidat->setCreatedAt(new DateTimeImmutable());
                $entityManager->persist($candidat);
                $entityManager->flush();
                return $this->redirectToRoute('app_candidat');
            }
            $this->addFlash("notice", "Impossible de contacter le serveur de Messsagerie");
            return $this->redirectToRoute('app_candidat');
        }

        $candidats = $candidatRepository->findAll();
        $total = ['votants' => 0, 'voix' => 0];
        foreach($candidats as $can){
            $resultats = $resultatRepository->findBy(['candidat' => $can->getId()]);
            $totalParCandidat = [];
            foreach($resultats as $resultat){
                $total['votants'] += $resultat->getNombreVotant();
                $total['voix'] += $resultat->getNombreVoix();
                $code = '' .$can->getCodeCENI();
                if(!array_key_exists($code, $totalParCandidat)){
                    $totalParCandidat[$code] = 0;
                }
                $totalParCandidat[$code] += $resultat->getNombreVoix();
            }
        }

        return $this->renderForm('candidat/index.html.twig', [
            'controller_name' => 'CandidatController',
            'form' => $form,
            'candidats' => $candidats,
            'rapport' => $total,
            'rapport_bv' => json_encode(array_values($totalParCandidat)),
            'bureau_votes' => json_encode(array_keys($totalParCandidat))
        ]);
    }
}
