<?php

namespace App\Entity;

use App\Repository\UserGradeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGradeRepository::class)]
class UserGrade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    #[ORM\JoinColumn(nullable: false)]
    private $grade;

    #[ORM\Column(type: 'boolean')]
    #[ORM\JoinColumn(nullable: false)]
    private $status;

    #[ORM\OneToOne(inversedBy: 'userGrade', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $user_id;

    #[ORM\OneToOne(inversedBy: 'userGrade', targetEntity: subject::class, cascade: ['persist', 'remove'])]
    private $subject_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrade(): ?float
    {
        return $this->grade;
    }

    public function setGrade(float $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
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

    public function getSubjectId(): ?subject
    {
        return $this->subject_id;
    }

    public function setSubjectId(?subject $subject_id): self
    {
        $this->subject_id = $subject_id;

        return $this;
    }
}
