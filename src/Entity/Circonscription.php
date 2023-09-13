<?php

namespace App\Entity;

use App\Repository\CirconscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CirconscriptionRepository::class)]
class Circonscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $code;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $nom;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\ManyToOne(targetEntity: Province::class, inversedBy: 'circonscriptions')]
    private $province;

    #[ORM\OneToMany(mappedBy: 'circonscription', targetEntity: Temoin::class)]
    private $temoins;

    #[ORM\OneToMany(mappedBy: 'cironscription', targetEntity: BureauVote::class)]
    private $bureauVotes;

    public function __construct()
    {
        $this->temoins = new ArrayCollection();
        $this->bureauVotes = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(?Province $province): self
    {
        $this->province = $province;

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
            $temoin->setCirconscription($this);
        }

        return $this;
    }

    public function removeTemoin(Temoin $temoin): self
    {
        if ($this->temoins->removeElement($temoin)) {
            // set the owning side to null (unless already changed)
            if ($temoin->getCirconscription() === $this) {
                $temoin->setCirconscription(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BureauVote>
     */
    public function getBureauVotes(): Collection
    {
        return $this->bureauVotes;
    }

    public function addBureauVote(BureauVote $bureauVote): self
    {
        if (!$this->bureauVotes->contains($bureauVote)) {
            $this->bureauVotes[] = $bureauVote;
            $bureauVote->setCironscription($this);
        }

        return $this;
    }

    public function removeBureauVote(BureauVote $bureauVote): self
    {
        if ($this->bureauVotes->removeElement($bureauVote)) {
            // set the owning side to null (unless already changed)
            if ($bureauVote->getCironscription() === $this) {
                $bureauVote->setCironscription(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }
}
