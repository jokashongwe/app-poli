<?php

namespace App\Entity;

use App\Repository\TemoinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: TemoinRepository::class)]
class Temoin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'temoin', targetEntity: Membre::class, cascade: ['persist'])]
    #[MaxDepth(2)]
    private $membre;

    #[ORM\Column(type: 'string', length: 255)]
    private $accreditation;

    #[ORM\ManyToOne(targetEntity: Circonscription::class, inversedBy: 'temoins')]
    #[MaxDepth(2)]
    private $circonscription;

    #[ORM\ManyToOne(targetEntity: BureauVote::class, inversedBy: 'temoins')]
    #[MaxDepth(2)]
    private $bureauVote;

    #[ORM\ManyToOne(targetEntity: Candidat::class, inversedBy: 'temoins')]
    private $candidat;

    #[ORM\OneToMany(mappedBy: 'temoin', targetEntity: Resultat::class)]
    private $resultats;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $backupCode;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    private $user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $sigle;

    public function __construct()
    {
        $this->resultats = new ArrayCollection();
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

    public function getAccreditation(): ?string
    {
        return $this->accreditation;
    }

    public function setAccreditation(string $accreditation): self
    {
        $this->accreditation = $accreditation;

        return $this;
    }

    public function getCirconscription(): ?Circonscription
    {
        return $this->circonscription;
    }

    public function setCirconscription(?Circonscription $circonscription): self
    {
        $this->circonscription = $circonscription;

        return $this;
    }

    public function getBureauVote(): ?BureauVote
    {
        return $this->bureauVote;
    }

    public function setBureauVote(?BureauVote $bureauVote): self
    {
        $this->bureauVote = $bureauVote;

        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    /**
     * @return Collection<int, Resultat>
     */
    public function getResultats(): Collection
    {
        return $this->resultats;
    }

    public function addResultat(Resultat $resultat): self
    {
        if (!$this->resultats->contains($resultat)) {
            $this->resultats[] = $resultat;
            $resultat->setTemoin($this);
        }

        return $this;
    }

    public function removeResultat(Resultat $resultat): self
    {
        if ($this->resultats->removeElement($resultat)) {
            // set the owning side to null (unless already changed)
            if ($resultat->getTemoin() === $this) {
                $resultat->setTemoin(null);
            }
        }

        return $this;
    }

    public function getBackupCode(): ?string
    {
        return $this->backupCode;
    }

    public function setBackupCode(?string $backupCode): self
    {
        $this->backupCode = $backupCode;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSerialized(): array
    {
        return [
            'id' => $this->id,
            
            'bureauVote' => '/api/bureau_vote/' . $this->bureauVote->getId()
            ,
            'circonscription' => '/api/circonscription/' . $this->circonscription->getId(),
            'membre' => [
                'path' => '/api/membre/' . $this->membre->getId(),
                'id' => $this->membre->getId(),
                'nom' => $this->membre->getNom(),
                'postnom' => $this->membre->getPostnom(),
                'prenom' => $this->membre->getPrenom(),
                'identification' => $this->membre->getNoidentification()
            ],
            'user' => $this->user,
        ];
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): self
    {
        $this->sigle = $sigle;

        return $this;
    }
}
