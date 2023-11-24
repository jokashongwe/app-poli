<?php

namespace App\Entity;

use App\Repository\SendernameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SendernameRepository::class)]
class Sendername
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $senderid;

    #[ORM\ManyToOne(targetEntity: Organisation::class, inversedBy: 'sendernames')]
    private $organisation;

    #[ORM\OneToMany(mappedBy: 'sendername', targetEntity: Diffusion::class)]
    private $diffusions;

    public function __construct()
    {
        $this->diffusions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderid(): ?string
    {
        return $this->senderid;
    }

    public function setSenderid(string $senderid): self
    {
        $this->senderid = $senderid;

        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * @return Collection<int, Diffusion>
     */
    public function getDiffusions(): Collection
    {
        return $this->diffusions;
    }

    public function addDiffusion(Diffusion $diffusion): self
    {
        if (!$this->diffusions->contains($diffusion)) {
            $this->diffusions[] = $diffusion;
            $diffusion->setSendername($this);
        }

        return $this;
    }

    public function removeDiffusion(Diffusion $diffusion): self
    {
        if ($this->diffusions->removeElement($diffusion)) {
            // set the owning side to null (unless already changed)
            if ($diffusion->getSendername() === $this) {
                $diffusion->setSendername(null);
            }
        }

        return $this;
    }
}
