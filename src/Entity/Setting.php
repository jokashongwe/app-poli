<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $president;

    #[ORM\Column(type: 'string', length: 255)]
    private $photoPresident;

    #[ORM\Column(type: 'string', length: 255)]
    private $nomparti;

    #[ORM\Column(type: 'string', length: 255)]
    private $messageEnregistrement;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $facebookURL;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $twitterURL;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $slogan;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPresident(): ?string
    {
        return $this->president;
    }

    public function setPresident(string $president): self
    {
        $this->president = $president;

        return $this;
    }

    public function getPhotoPresident(): ?string
    {
        return $this->photoPresident;
    }

    public function setPhotoPresident(string $photoPresident): self
    {
        $this->photoPresident = $photoPresident;

        return $this;
    }

    public function getNomparti(): ?string
    {
        return $this->nomparti;
    }

    public function setNomparti(string $nomparti): self
    {
        $this->nomparti = $nomparti;

        return $this;
    }

    public function getMessageEnregistrement(): ?string
    {
        return $this->messageEnregistrement;
    }

    public function setMessageEnregistrement(string $messageEnregistrement): self
    {
        $this->messageEnregistrement = $messageEnregistrement;

        return $this;
    }

    public function getFacebookURL(): ?string
    {
        return $this->facebookURL;
    }

    public function setFacebookURL(?string $facebookURL): self
    {
        $this->facebookURL = $facebookURL;

        return $this;
    }

    public function getTwitterURL(): ?string
    {
        return $this->twitterURL;
    }

    public function setTwitterURL(?string $twitterURL): self
    {
        $this->twitterURL = $twitterURL;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan): self
    {
        $this->slogan = $slogan;

        return $this;
    }
}
