<?php

namespace App\Entity;

use App\Repository\SendernameRepository;
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
}
