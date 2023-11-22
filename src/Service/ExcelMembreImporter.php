<?php

namespace App\Service;

use App\Entity\Federation;
use App\Entity\Membre;
use App\Entity\Province;
use App\Entity\Qualite;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ExcelMembreImporter
{

    private UploadedFile $file;
    private string $filename = "";
    private ManagerRegistry $managerRegistery;
    private User $user;
    private $tags;

    function __construct(UploadedFile $file, ManagerRegistry $em, User $user = null, $tags=null)
    {
        $this->file = $file;
        $this->readFile();
        $this->managerRegistery = $em;
        $this->user = $user;
        $this->tags = $tags;
    }

    private function readFile()
    {
        $extension = $this->file->guessExtension();
        if (!$extension) {
            $extension = 'bin';
        }
        $filename = rand(1, 99999) . '.' . $extension;
        $this->file->move('../public/excels', $filename);
        $this->filename = "../public/excels/" . $filename;
    }

    private function getGenre(string $genre)
    {
        $genre = strtolower($genre);
        if ($genre == "m" || $genre == "h" || $genre == "homme" ||  $genre == "masculin") {
            return "Homme";
        }
        return "Femme";
    }

    public function processData()
    {
        $inputFileType = IOFactory::identify($this->filename);
        $reader = IOFactory::createReader($inputFileType);
        //$chunkSize = 50;
        $chunkFilter = new ChunkReaderFilter();
        $chunkFilter->setRows(1, 10000);
        $reader->setReadFilter($chunkFilter);
        //for ($startRow = 1; $startRow <= 240; $startRow += $chunkSize) {
        //$chunkFilter->setRows($startRow, $chunkSize);
        $spreadsheet = $reader->load($this->filename);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        //$tag = 
        //dd(sizeof($sheetData));
        foreach ($sheetData as $index => $membreData) {
            if ($index == 1) continue; //on ignore la premiere ligne
            $organisation = $this->user->getOrganisation();
            //if(!is_null($membreData["D"]) && $this->isMemberExist($membreData["D"], $organisation)) continue;
            $membre = new Membre();
            $membre->setNom($membreData["A"]);
            $membre->setPostnom($membreData["B"]);
            $membre->setPrenom($membreData["C"]);
            $phone = $membreData["D"];
            $phone = "+243" . $phone;
            $tags = $this->tags;
            if(empty($tags)){
                $tags = $organisation->getTags();
            }
            
            foreach ($tags as $tag) {
                $membre->addTag($tag);
            }
            $membre->setTelephone($phone);
            /*
                    $membre->setGenre($this->getGenre($membreData["D"]));
                    $membre->setDatenaissance(\DateTime::createFromFormat("d/m/Y", $membreData["E"]));
                    $membre->setAdresse($membreData["F"]);
                    $membre->setQualite($this->getOrCreateQualite("titre", Qualite::class, $membreData["G"], $this->managerRegistery));
                    $membre->setFederation($this->getOrCreateFederation($membreData["I"], $membreData["H"],  $membreData["J"], $this->managerRegistery));
                    $membre->setSousfederation($membreData["I"]);
                */
            $membre->setDateadhesion(new \DateTimeImmutable());
            $membre->setNoidentification($this->generateIdNumber());
            $membre->setOrganisation($organisation);
            //dd($this->tags);
            $this->managerRegistery->getManager()->persist($membre);
            $this->managerRegistery->getManager()->flush();
            //dd($organisation);
        }
        $this->managerRegistery->getManager()->flush();
        //}
        unlink($this->filename);
    }

    private function isMemberExist($telephone, $organisation)
    {
        $result = $this->managerRegistery->getRepository(Membre::class)->findOneBy([
            'telephone' => $telephone,
            'organisation' => $organisation
        ]);

        return !is_null($result);
    }

    private function generateIdNumber()
    {
        $part01 = rand(3000, 9999);
        $part02 = rand(300000, 999999);
        $part03 = date("Y");
        return '' . $part01 . '/' . $part02 . '/' . $part03;
    }

    private function getOrCreateQualite(string $criteria, string $className, string $value, ManagerRegistry $em)
    {
        $obj = $em->getRepository($className)->findOneBy([
            $criteria => $value
        ]);
        if (!is_null($obj)) {
            return $obj;
        }

        $namespace = '';
        $fully_qualified_class_name = "$namespace\\$className";
        $obj = new $fully_qualified_class_name;

        $criteria = ucfirst($criteria);
        $methodName = "set$criteria";
        $obj->$methodName($value);

        $eManager = $em->getManager();
        $eManager->persist($obj);
        $eManager->flush();
        return $obj;
    }

    private function getOrCreateFederation(string $nom, string $federation, string $province, ManagerRegistry $em)
    {
        $fede = $em->getRepository(Federation::class)->findOneBy([
            "nom" => $nom
        ]);
        if (!is_null($fede)) {
            return $fede;
        }
        $entityManager = $em->getManager();
        // On recherche la province
        $prov = $em->getRepository(Province::class)->findOneBy([
            'nom' => $province
        ]);

        if (is_null($province)) {
            //creation de la province
            $prov = new Province();
            $prov->setNom($province);
            $entityManager->persist($prov);
            $entityManager->flush();
        }
        //on recherche la federation parente
        $fedeParente = $em->getRepository(Federation::class)->findOneBy([
            "nom" => $federation
        ]);
        if (is_null($fedeParente)) {
            $fedeParente = new Federation();
            $fedeParente->setNom($federation);
            $fedeParente->setProvince($prov);
            $entityManager->persist($fedeParente);
            $entityManager->flush();
        }
        $fede = new Federation();
        $fede->setNom($nom);
        $fede->setFederation($fedeParente);
        $fede->setProvince($prov);
        $entityManager->persist($fede);
        $entityManager->flush();
        return $fede;
    }
}
