<?php

namespace App\Entity;

use App\Repository\MembreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private $prenom;

    #[ORM\Column(type: 'string', length: 255)]
    private $postnom;

    #[ORM\Column(type: 'date', nullable:true)]
    private $datenaissance;

    #[ORM\Column(type: 'string', length: 255)]
    private $adresse;

    #[ORM\ManyToOne(targetEntity: Federation::class, inversedBy: 'membres')]
    #[MaxDepth(2)]
    private $federation;

    #[ORM\OneToMany(mappedBy: 'membre', targetEntity: Cotisation::class)]
    private $cotisations;

    #[ORM\Column(type: 'string', length: 255)]
    private $noidentification;

    #[ORM\Column(type: 'string', length: 255)]
    private $genre;

    #[ORM\Column(type: 'string', length: 255)]
    private $telephone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $numerocarte;

    #[ORM\Column(type: 'date', nullable: true)]
    private $dateadhesion;

    #[ORM\Column(type: 'text', nullable: true)]
    private $avatar;

    #[ORM\ManyToOne(targetEntity: Qualite::class, inversedBy: 'membres')]
    private $qualite;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $sousfederation;

    #[ORM\Column(type: 'boolean', nullable: true, options: ["default" => false])]
    private $apayercotisation;

    #[ORM\Column(type: 'boolean', nullable: true, options: ["default" => false] )]
    private $aunecarte;

    #[ORM\OneToOne(mappedBy: 'membre', targetEntity: Temoin::class, cascade: ['persist'])]
    #[MaxDepth(2)]
    private $temoin;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'membres')]
    private $tags;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $memSection;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $pointFocal;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $visible;

    public function __construct()
    {
        $this->cotisations = new ArrayCollection();
        $this->tags = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPostnom(): ?string
    {
        return $this->postnom;
    }

    public function setPostnom(string $postnom): self
    {
        $this->postnom = $postnom;

        return $this;
    }

    public function getDatenaissance(): ?\DateTimeInterface
    {
        return $this->datenaissance;
    }

    public function setDatenaissance(\DateTimeInterface $datenaissance): self
    {
        $this->datenaissance = $datenaissance;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getFederation(): ?Federation
    {
        return $this->federation;
    }

    public function setFederation(?Federation $federation): self
    {
        $this->federation = $federation;

        return $this;
    }

    /**
     * @return Collection<int, Cotisation>
     */
    public function getCotisations(): Collection
    {
        return $this->cotisations;
    }

    public function addCotisation(Cotisation $cotisation): self
    {
        if (!$this->cotisations->contains($cotisation)) {
            $this->cotisations[] = $cotisation;
            $cotisation->setMembre($this);
        }

        return $this;
    }

    public function removeCotisation(Cotisation $cotisation): self
    {
        if ($this->cotisations->removeElement($cotisation)) {
            // set the owning side to null (unless already changed)
            if ($cotisation->getMembre() === $this) {
                $cotisation->setMembre(null);
            }
        }

        return $this;
    }

    public function getNoidentification(): ?string
    {
        return $this->noidentification;
    }

    public function setNoidentification(string $noidentification): self
    {
        $this->noidentification = $noidentification;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNumerocarte(): ?string
    {
        return $this->numerocarte;
    }

    public function setNumerocarte(?string $numerocarte): self
    {
        $this->numerocarte = $numerocarte;

        return $this;
    }

    public function getDateadhesion(): ?\DateTimeInterface
    {
        return $this->dateadhesion;
    }

    public function setDateadhesion(?\DateTimeInterface $dateadhesion): self
    {
        $this->dateadhesion = $dateadhesion;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getQualite(): ?Qualite
    {
        return $this->qualite;
    }

    public function setQualite(?Qualite $qualite): self
    {
        $this->qualite = $qualite;

        return $this;
    }

    public function getSousfederation(): ?string
    {
        return $this->sousfederation;
    }

    public function setSousfederation(?string $sousfederation): self
    {
        $this->sousfederation = $sousfederation;

        return $this;
    }

    public function getApayercotisation(): ?bool
    {
        return $this->apayercotisation;
    }

    public function setApayercotisation(?bool $apayercotisation): self
    {
        $this->apayercotisation = $apayercotisation;

        return $this;
    }

    public function getAunecarte(): ?bool
    {
        return $this->aunecarte;
    }

    public function setAunecarte(?bool $aunecarte): self
    {
        $this->aunecarte = $aunecarte;

        return $this;
    }

    public function getTemoin(): ?Temoin
    {
        return $this->temoin;
    }

    public function setTemoin(?Temoin $temoin): self
    {
        // unset the owning side of the relation if necessary
        if ($temoin === null && $this->temoin !== null) {
            $this->temoin->setMembre(null);
        }

        // set the owning side of the relation if necessary
        if ($temoin !== null && $temoin->getMembre() !== $this) {
            $temoin->setMembre($this);
        }

        $this->temoin = $temoin;

        return $this;
    }

    public function __toString()
    {
        return $this->nom . ' ' . $this->postnom . ' ' . $this->prenom;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addMembre($this);
        }

        return $this;
    }

    public function emptyTags(): self
    {
        foreach($this->tags as $tag){
            if($tag->getCode() != 'GENERAL'){
                $this->removeTag($tag);
            }
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeMembre($this);
        }

        return $this;
    }

    public function getMemSection(): ?string
    {
        return $this->memSection;
    }

    public function setMemSection(?string $memSection): self
    {
        $this->memSection = $memSection;

        return $this;
    }

    public function getPointFocal(): ?string
    {
        return $this->pointFocal;
    }

    public function setPointFocal(?string $pointFocal): self
    {
        $this->pointFocal = $pointFocal;

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

}
