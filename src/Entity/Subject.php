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

    #[ORM\Column(type: 'integer')]
    private $points;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: UserGrade::class, fetch: 'EAGER')]
    private $userGrades;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: SubjectDate::class, fetch: 'EAGER')]
    private $subjectDates;

    #[ORM\ManyToOne(targetEntity: StudyLevel::class, fetch:'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private $level;

    public function __construct()
    {
        $this->userGrades = new ArrayCollection();
        $this->subjectDates = new ArrayCollection();
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

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

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
}
