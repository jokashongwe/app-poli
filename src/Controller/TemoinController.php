<?php

namespace App\Controller;

use App\Entity\Resultat;
use App\Entity\Temoin;
use App\Entity\User;
use App\Form\Type\TemoinType;
use App\Repository\CandidatRepository;
use App\Repository\ResultatRepository;
use App\Repository\SettingRepository;
use App\Repository\TemoinRepository;
use App\Repository\UserRepository;
use App\Service\MessageService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TemoinController extends AbstractController
{
    #[Route('/temoin', name: 'app_temoin')]
    public function index(
        Request $request,
        ManagerRegistry $doctrine,
        TemoinRepository $temoinRepository,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        SettingRepository $settingRepository
    ): Response {
        $temoin = new Temoin();

        $form = $this->createForm(TemoinType::class, $temoin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $setting = $settingRepository->findAll();
            $sigle = "";
            if (!empty($setting)) {
                $setting = $setting[0];
                $sigle = $setting->getSigle();
            }

            $temoin = $form->getData();
            //dd($temoin);
            /**
             * création du User
             * Notification par SMS
             */
            $membre = $temoin->getMembre();
            $telephone = $membre->getTelephone();

            $entityManager = $doctrine->getManager();
            $code =  rand(300000, 999999);
            $temoin->setBackupCode($code);
            $message = "Bonjour, vous etes desormais temoin dans le regroupement/parti $sigle, PIN: " . $code;
            $msgService = new MessageService($this->getParameter('app.bulksmstoken'));
            $result = $msgService->sendManySMS(
                $message,
                [$telephone],
                $this->getParameter('app.senderid'),
                $this->getParameter('app.sendermode')
            );
            $telephone = str_replace('+243', '0', $telephone);
            if ($result['http_status'] == 201) {
                $temoinUser = $userRepository->findOneBy(['username' => $telephone]);
                if (empty($temoinUser)) {
                    $temoinUser = new User();
                }
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
            } else {
                $this->addFlash("notice", "Impossible de contacter le serveur de Messsagerie");
            }

            return $this->redirectToRoute('app_temoin');
        }


        return $this->renderForm('temoin/index.html.twig', [
            'controller_name' => 'TemoinController',
            'form' => $form,
            'temoins' => $temoinRepository->findAll()
        ]);
    }

    #[Route('/temoin/delete/{id}', name: 'app_temoin_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, TemoinRepository $temoinRepository, $id)
    {
        $temoin = $temoinRepository->find($id);
        if (is_null($temoin)) {
            return $this->redirectToRoute('app_temoin');
        }
        $user = $temoin->getUser();
        $em = $doctrine->getManager();
        $em->remove($user);
        $em->remove($temoin);
        $em->flush();
        return $this->redirectToRoute('app_temoin');
    }

    #[Route('api/temoin/info', name: 'app_temoin_info')]
    public function get_info(Request $request, ManagerRegistry $doctrine, TemoinRepository $temoinRepository)
    {

        $temoin = $temoinRepository->findOneBy(['user' => $this->getUser()]);
        //$result = $serializer->normalize($temoin, null, [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);
        return $this->json([
            'data' => $temoin->getSerialized(),
            'success' => true
        ]);
    }

    #[Route('api/result/{id}', name: 'app_temoin_upload')]
    public function upload_result(Request $request, ManagerRegistry $doctrine, CandidatRepository $candidatRepository, TemoinRepository $temoinRepository, ResultatRepository $resultatRepository, $id)
    {
        try {
            $candidats = $request->get("candidats");
            
            //$nombreVotants = $request->get("nombre_votant");
            //$nombreVoix = $request->get("nombre_voix");
            if (empty($candidats)) {
                //$candidats = json_decode($candidats, true);
                return $this->json(['data' => [], 'message' => 'candidats cannot be empty', 'success' => false], 400);
            }
            $candidats = json_decode($candidats, true);
            $files = $request->files->all();
            $filenames = $this->upload_files($files);
            $temoin = $temoinRepository->find($id);
            $codeBV = '' . $temoin->getBureauVote()->getCode();
            $resultat = $resultatRepository->findBy(['codeBV' => $codeBV, 'temoin' => $temoin]);
            if(!empty($resultat)){
                return $this->json(['data' => [], 'message' => 'Un résultat existe déjà pour ce temoin', 'success' => true]);
            }
            $nombreVoix = -1;
            $nombreVotants = -1;
            $manager = $doctrine->getManager();
            
            foreach($candidats as $can){
                $candidat = $candidatRepository->findOneBy(['codeCENI' => $can['numero']]);
                if(!is_null($candidat)){
                    $nombreVoix = intval($can['voix']);
                    $nombreVotants = intval($can['votants']);
                    $resultat = new Resultat();
                    $resultat->setTemoin($temoin);
                    $resultat->setCandidat($candidat);
                    $resultat->setCodeBV($codeBV);
                    $resultat->setProceVerbaux($filenames);
                    $resultat->setNombreVoix($nombreVoix);
                    $resultat->setNombreVotant($nombreVotants);
                    $resultat->setAutres($candidats);
                    $manager->persist($resultat);
                }
            }
            $manager->flush();
            return $this->json(['data' => $resultat->getSerialize() , 'success' => true]);
        } catch (\Throwable $th) {
            return $this->json(['data' => null, 'message' => $th->getMessage(), 'success' => false]);
        }
    }

    private function upload_files($files)
    {
        $filenames = [];
        foreach ($files as $file) {
            $extension = $file->guessExtension();
            if (!$extension) {
                // extension cannot be guessed
                $extension = 'bin';
            }
            $filename = rand(1, 99999) . '.' . $extension;
            $file->move('../public/uploads/results', $filename);
            $filename = "uploads/results/" . $filename;
            array_push($filenames, $filename);
        }
        return $filenames;
    }
}
