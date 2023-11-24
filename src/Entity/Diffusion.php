<?php

namespace App\Entity;

use App\Repository\DiffusionRepository;
use DateTime;
use DateTimeImmutable;
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

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $visible;

    #[ORM\Column(type: 'json', nullable: true)]
    private $tags = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private $canal = [];

    #[ORM\Column(type: 'text', nullable: true)]
    private $richText;

    #[ORM\ManyToOne(targetEntity: Organisation::class, inversedBy: 'diffusions')]
    private $organisation;

    #[ORM\ManyToOne(targetEntity: Sendername::class, inversedBy: 'diffusions')]
    private $sendername;

    public function __construct()
    {
        if(is_null($this->startDate)){
            $this->setStartDate(new DateTime());
        }
    }

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

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getCanal(): ?array
    {
        return $this->canal;
    }

    public function setCanal(?array $canal): self
    {
        $this->canal = $canal;

        return $this;
    }

    public function getRichText(): ?string
    {
        return $this->richText;
    }

    public function setRichText(?string $richText): self
    {
        $this->richText = $richText;

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

    public function getSendername(): ?Sendername
    {
        return $this->sendername;
    }

    public function setSendername(?Sendername $sendername): self
    {
        $this->sendername = $sendername;

        return $this;
    }
}
