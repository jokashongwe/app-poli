<?php

namespace App\Entity;

use App\Repository\ResultatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultatRepository::class)]
class Resultat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Temoin::class, inversedBy: 'resultats')]
    private $temoin;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nombreVotant;

    #[ORM\Column(type: 'integer')]
    private $nombreVoix;

    #[ORM\Column(type: 'json')]
    private $proceVerbaux = [];

    #[ORM\ManyToOne(targetEntity: Candidat::class, inversedBy: 'resultats')]
    private $candidat;

    #[ORM\Column(type: 'json', nullable: true)]
    private $autres = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $codeBV;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemoin(): ?Temoin
    {
        return $this->temoin;
    }

    public function setTemoin(?Temoin $temoin): self
    {
        $this->temoin = $temoin;

        return $this;
    }

    public function getNombreVotant(): ?int
    {
        return $this->nombreVotant;
    }

    public function setNombreVotant(?int $nombreVotant): self
    {
        $this->nombreVotant = $nombreVotant;

        return $this;
    }

    public function getNombreVoix(): ?int
    {
        return $this->nombreVoix;
    }

    public function setNombreVoix(int $nombreVoix): self
    {
        $this->nombreVoix = $nombreVoix;

        return $this;
    }

    public function getProceVerbaux(): ?array
    {
        return $this->proceVerbaux;
    }

    public function setProceVerbaux(array $proceVerbaux): self
    {
        $this->proceVerbaux = $proceVerbaux;

        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    public function getAutres(): ?array
    {
        return $this->autres;
    }

    public function setAutres(?array $autres): self
    {
        $this->autres = $autres;

        return $this;
    }

    public function getSerialize()
    {
        $candidat = $this->candidat;
        $candidat_id = $candidat ? $this->candidat->getId() : '';
        return [
            'id' => $this->id,
            'nombre_votants' => $this->nombreVotant,
            'nombre_voix' => $this->nombreVoix,
            'candidat' => $candidat ? [
                'id' => $candidat_id,
                'path' => '/path/candidat/' . $candidat_id
            ]: null,
            'photos' => $this->proceVerbaux,
            'autres' => $this->autres
        ];
    }

    public function getCodeBV(): ?string
    {
        return $this->codeBV;
    }

    public function setCodeBV(?string $codeBV): self
    {
        $this->codeBV = $codeBV;

        return $this;
    }
}
