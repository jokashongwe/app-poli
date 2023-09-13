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
}
