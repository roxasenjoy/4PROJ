<?php

namespace App\Entity;

use App\Repository\IntervenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntervenantRepository::class)]
class Intervenant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'intervenant', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $user_id;

    #[ORM\OneToMany(mappedBy: 'intervenant', targetEntity: subject::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $subject_id;

    public function __construct()
    {
        $this->subject_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, subject>
     */
    public function getSubjectId(): Collection
    {
        return $this->subject_id;
    }

    public function addSubjectId(subject $subjectId): self
    {
        if (!$this->subject_id->contains($subjectId)) {
            $this->subject_id[] = $subjectId;
            $subjectId->setIntervenant($this);
        }

        return $this;
    }

    public function removeSubjectId(subject $subjectId): self
    {
        if ($this->subject_id->removeElement($subjectId)) {
            // set the owning side to null (unless already changed)
            if ($subjectId->getIntervenant() === $this) {
                $subjectId->setIntervenant(null);
            }
        }

        return $this;
    }
}
