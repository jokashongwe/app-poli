<?php

namespace App\Entity;

use App\Repository\DiffusionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiffusionRepository::class)]
class Diffusion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $titre;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $startDate;

    #[ORM\Column(type: 'integer')]
    private $numberOfMembers;

    #[ORM\Column(type: 'json', nullable: true)]
    private $federations = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private $provinces = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getNumberOfMembers(): ?int
    {
        return $this->numberOfMembers;
    }

    public function setNumberOfMembers(int $numberOfMembers): self
    {
        $this->numberOfMembers = $numberOfMembers;

        return $this;
    }

    public function getFederations(): ?array
    {
        return $this->federations;
    }

    public function setFederations(array $federations): self
    {
        $this->federations = $federations;

        return $this;
    }

    public function getProvinces(): ?array
    {
        return $this->provinces;
    }

    public function setProvinces(?array $provinces): self
    {
        $this->provinces = $provinces;

        return $this;
    }
}
