<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $fullName;

    #[ORM\Column(type: 'integer')]
    #[ORM\JoinColumn(nullable: false)]
    private $points;

    #[ORM\Column(type: 'datetime')]
    #[ORM\JoinColumn(nullable: false)]
    private $date_begin;

    #[ORM\Column(type: 'datetime')]
    #[ORM\JoinColumn(nullable: false)]
    private $date_end;

    #[ORM\OneToOne(mappedBy: 'subject_id', targetEntity: UserGrade::class, cascade: ['persist', 'remove'])]
    private $userGrade;

    #[ORM\ManyToOne(targetEntity: Intervenant::class, inversedBy: 'subject_id')]
    private $intervenant;

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

    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->date_begin;
    }

    public function setDateBegin(\DateTimeInterface $date_begin): self
    {
        $this->date_begin = $date_begin;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }

    public function getUserGrade(): ?UserGrade
    {
        return $this->userGrade;
    }

    public function setUserGrade(?UserGrade $userGrade): self
    {
        // unset the owning side of the relation if necessary
        if ($userGrade === null && $this->userGrade !== null) {
            $this->userGrade->setSubjectId(null);
        }

        // set the owning side of the relation if necessary
        if ($userGrade !== null && $userGrade->getSubjectId() !== $this) {
            $userGrade->setSubjectId($this);
        }

        $this->userGrade = $userGrade;

        return $this;
    }

    public function getIntervenant(): ?Intervenant
    {
        return $this->intervenant;
    }

    public function setIntervenant(?Intervenant $intervenant): self
    {
        $this->intervenant = $intervenant;

        return $this;
    }
}
