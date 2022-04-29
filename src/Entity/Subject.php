<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
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

    #[ORM\ManyToOne(targetEntity: StudyLevel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $level;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: SubjectDate::class, orphanRemoval: true)]
    private $subjectDates;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: UserGrade::class, orphanRemoval: true)]
    private $userGrades;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Intervenant::class)]
    private $intervenant;

    public function __construct()
    {
        $this->subjectDates = new ArrayCollection();
        $this->userGrades = new ArrayCollection();
        $this->intervenant = new ArrayCollection();
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

    public function getLevel(): ?StudyLevel
    {
        return $this->level;
    }

    public function setLevel(?StudyLevel $level): self
    {
        $this->level = $level;

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

    /**
     * @return Collection<int, UserGrade>
     */
    public function getUserGrades(): Collection
    {
        return $this->userGrades;
    }

    public function addUserGrade(UserGrade $userGrade): self
    {
        if (!$this->userGrades->contains($userGrade)) {
            $this->userGrades[] = $userGrade;
            $userGrade->setSubject($this);
        }

        return $this;
    }

    public function removeUserGrade(UserGrade $userGrade): self
    {
        if ($this->userGrades->removeElement($userGrade)) {
            // set the owning side to null (unless already changed)
            if ($userGrade->getSubject() === $this) {
                $userGrade->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Intervenant>
     */
    public function getIntervenant(): Collection
    {
        return $this->intervenant;
    }

    public function addIntervenant(Intervenant $intervenant): self
    {
        if (!$this->intervenant->contains($intervenant)) {
            $this->intervenant[] = $intervenant;
            $intervenant->setSubject($this);
        }

        return $this;
    }

    public function removeIntervenant(Intervenant $intervenant): self
    {
        if ($this->intervenant->removeElement($intervenant)) {
            // set the owning side to null (unless already changed)
            if ($intervenant->getSubject() === $this) {
                $intervenant->setSubject(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
