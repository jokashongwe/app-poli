<?php

namespace App\Entity;

use App\Repository\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
class Organisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: User::class)]
    private $users;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Member::class)]
    private $members;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Federation::class)]
    private $federations;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $credits;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Payments::class)]
    private $payments;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Tag::class)]
    private $tags;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Membre::class)]
    private $membres;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Diffusion::class)]
    private $diffusions;

    #[ORM\Column(type: 'json', nullable: true)]
    private $senderNames = [];

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->federations = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->membres = new ArrayCollection();
        $this->diffusions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setOrganisation($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getOrganisation() === $this) {
                $user->setOrganisation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setOrganisation($this);
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getOrganisation() === $this) {
                $member->setOrganisation(null);
            }
        }

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
            $federation->setOrganisation($this);
        }

        return $this;
    }

    public function removeFederation(Federation $federation): self
    {
        if ($this->federations->removeElement($federation)) {
            // set the owning side to null (unless already changed)
            if ($federation->getOrganisation() === $this) {
                $federation->setOrganisation(null);
            }
        }

        return $this;
    }

    public function getCredits(): ?string
    {
        return $this->credits;
    }

    public function setCredits(?string $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * @return Collection<int, Payments>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payments $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setOrganisation($this);
        }

        return $this;
    }

    public function removePayment(Payments $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getOrganisation() === $this) {
                $payment->setOrganisation(null);
            }
        }

        return $this;
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
            $tag->setOrganisation($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            // set the owning side to null (unless already changed)
            if ($tag->getOrganisation() === $this) {
                $tag->setOrganisation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Membre>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Membre $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres[] = $membre;
            $membre->setOrganisation($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            // set the owning side to null (unless already changed)
            if ($membre->getOrganisation() === $this) {
                $membre->setOrganisation(null);
            }
        }

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
            $diffusion->setOrganisation($this);
        }

        return $this;
    }

    public function removeDiffusion(Diffusion $diffusion): self
    {
        if ($this->diffusions->removeElement($diffusion)) {
            // set the owning side to null (unless already changed)
            if ($diffusion->getOrganisation() === $this) {
                $diffusion->setOrganisation(null);
            }
        }

        return $this;
    }

    public function getSenderNames(): ?array
    {
        return $this->senderNames;
    }

    public function setSenderNames(?array $senderNames): self
    {
        $this->senderNames = $senderNames;

        return $this;
    }
}
