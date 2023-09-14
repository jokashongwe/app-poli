<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatRepository::class)]
class Candidat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Membre::class, cascade: ['persist', 'remove'])]
    private $membre;

    #[ORM\Column(type: 'string', length: 255)]
    private $categorie;

    #[ORM\Column(type: 'string', length: 255)]
    private $typeElection;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'candidat', targetEntity: Temoin::class)]
    private $temoins;

    #[ORM\Column(type: 'string', length: 255)]
    private $regroupement;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $parti;

    #[ORM\Column(type: 'string', length: 255)]
    private $codeCENI;

    public function __construct()
    {
        $this->temoins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): self
    {
        $this->membre = $membre;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getTypeElection(): ?string
    {
        return $this->typeElection;
    }

    public function setTypeElection(string $typeElection): self
    {
        $this->typeElection = $typeElection;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Temoin>
     */
    public function getTemoins(): Collection
    {
        return $this->temoins;
    }

    public function addTemoin(Temoin $temoin): self
    {
        if (!$this->temoins->contains($temoin)) {
            $this->temoins[] = $temoin;
            $temoin->setCandidat($this);
        }

        return $this;
    }

    public function removeTemoin(Temoin $temoin): self
    {
        if ($this->temoins->removeElement($temoin)) {
            // set the owning side to null (unless already changed)
            if ($temoin->getCandidat() === $this) {
                $temoin->setCandidat(null);
            }
        }

        return $this;
    }

    public function getRegroupement(): ?string
    {
        return $this->regroupement;
    }

    public function setRegroupement(string $regroupement): self
    {
        $this->regroupement = $regroupement;

        return $this;
    }

    public function getParti(): ?string
    {
        return $this->parti;
    }

    public function setParti(?string $parti): self
    {
        $this->parti = $parti;

        return $this;
    }

    public function getCodeCENI(): ?string
    {
        return $this->codeCENI;
    }

    public function setCodeCENI(string $codeCENI): self
    {
        $this->codeCENI = $codeCENI;

        return $this;
    }
}
