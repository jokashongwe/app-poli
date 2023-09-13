<?php

namespace App\Entity;

use App\Repository\BureauVoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BureauVoteRepository::class)]
class BureauVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $code;

    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $commune;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $territoire;

    #[ORM\OneToMany(mappedBy: 'bureauVote', targetEntity: Temoin::class)]
    private $temoins;

    #[ORM\ManyToOne(targetEntity: Circonscription::class, inversedBy: 'bureauVotes')]
    private $cironscription;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $codeCentre;

    public function __construct()
    {
        $this->temoins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(?string $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getTerritoire(): ?string
    {
        return $this->territoire;
    }

    public function setTerritoire(?string $territoire): self
    {
        $this->territoire = $territoire;

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
            $temoin->setBureauVote($this);
        }

        return $this;
    }

    public function removeTemoin(Temoin $temoin): self
    {
        if ($this->temoins->removeElement($temoin)) {
            // set the owning side to null (unless already changed)
            if ($temoin->getBureauVote() === $this) {
                $temoin->setBureauVote(null);
            }
        }

        return $this;
    }

    public function getCironscription(): ?Circonscription
    {
        return $this->cironscription;
    }

    public function setCironscription(?Circonscription $cironscription): self
    {
        $this->cironscription = $cironscription;

        return $this;
    }

    public function getCodeCentre(): ?string
    {
        return $this->codeCentre;
    }

    public function setCodeCentre(?string $codeCentre): self
    {
        $this->codeCentre = $codeCentre;

        return $this;
    }
}
