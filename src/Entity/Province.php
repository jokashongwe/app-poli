<?php

namespace App\Entity;

use App\Repository\ProvinceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProvinceRepository::class)]
class Province
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\OneToMany(mappedBy: 'province', targetEntity: Federation::class)]
    private $federations;

    #[ORM\OneToMany(mappedBy: 'province', targetEntity: Circonscription::class)]
    private $circonscriptions;

    public function __construct()
    {
        $this->federations = new ArrayCollection();
        $this->circonscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Federation>
     */
    public function getFederations(): Collection
    {
        return $this->federations;
    }

    public function addFederation(Federation $federation): self
    {
        if (!$this->federations->contains($federation)) {
            $this->federations[] = $federation;
            $federation->setProvince($this);
        }

        return $this;
    }

    public function removeFederation(Federation $federation): self
    {
        if ($this->federations->removeElement($federation)) {
            // set the owning side to null (unless already changed)
            if ($federation->getProvince() === $this) {
                $federation->setProvince(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Circonscription>
     */
    public function getCirconscriptions(): Collection
    {
        return $this->circonscriptions;
    }

    public function addCirconscription(Circonscription $circonscription): self
    {
        if (!$this->circonscriptions->contains($circonscription)) {
            $this->circonscriptions[] = $circonscription;
            $circonscription->setProvince($this);
        }

        return $this;
    }

    public function removeCirconscription(Circonscription $circonscription): self
    {
        if ($this->circonscriptions->removeElement($circonscription)) {
            // set the owning side to null (unless already changed)
            if ($circonscription->getProvince() === $this) {
                $circonscription->setProvince(null);
            }
        }

        return $this;
    }
}
