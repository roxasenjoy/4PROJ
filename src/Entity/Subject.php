<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Le cours est déjà présent.')]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $fullName;

    #[ORM\Column(type: 'float')]
    private $points;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: SubjectDate::class, fetch: 'EAGER', orphanRemoval: true)]
    private $subjectDates;

    #[ORM\ManyToOne(targetEntity: StudyLevel::class, fetch:'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private $level;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Intervenant::class, orphanRemoval: true)]
    private $intervenants;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: UserGrade::class, orphanRemoval: true)]
    private $userGrade;


    public function __construct()
    {
        $this->subjectDates = new ArrayCollection();
        $this->intervenants = new ArrayCollection();
        $this->userGrade = new ArrayCollection();
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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(float $points): self
    {
        $this->points = $points;

        return $this;
    }
    /**
     * @return Collection<int, SubjectDate>
     */
    public function getSubjectDates(): Collection
    {
        return $this->subjectDates;
    }

    public function addSubjectDate(SubjectDate $subjectDate): self
    {
        if (!$this->subjectDates->contains($subjectDate)) {
            $this->subjectDates[] = $subjectDate;
            $subjectDate->setSubject($this);
        }

        return $this;
    }

    public function removeSubjectDate(SubjectDate $subjectDate): self
    {
        if ($this->subjectDates->removeElement($subjectDate)) {
            // set the owning side to null (unless already changed)
            if ($subjectDate->getSubject() === $this) {
                $subjectDate->setSubject(null);
            }
        }

        return $this;
    }

    public function getLevel(): ?studyLevel
    {
        return $this->level;
    }

    public function setLevel(?studyLevel $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, Intervenant>
     */
    public function getIntervenants(): Collection
    {
        return $this->intervenants;
    }

    public function addIntervenant(Intervenant $intervenant): self
    {
        if (!$this->intervenants->contains($intervenant)) {
            $this->intervenants[] = $intervenant;
            $intervenant->setSubject($this);
        }

        return $this;
    }

    public function removeIntervenant(Intervenant $intervenant): self
    {
        if ($this->intervenants->removeElement($intervenant)) {
            // set the owning side to null (unless already changed)
            if ($intervenant->getSubject() === $this) {
                $intervenant->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserGrade>
     */
    public function getUserGrade(): Collection
    {
        return $this->userGrade;
    }

    public function addUserGrade(UserGrade $userGrade): self
    {
        if (!$this->userGrade->contains($userGrade)) {
            $this->userGrade[] = $userGrade;
            $userGrade->setSubject($this);
        }

        return $this;
    }

    public function removeUserGrade(UserGrade $userGrade): self
    {
        if ($this->userGrade->removeElement($userGrade)) {
            // set the owning side to null (unless already changed)
            if ($userGrade->getSubject() === $this) {
                $userGrade->setSubject(null);
            }
        }

        return $this;
    }
}
