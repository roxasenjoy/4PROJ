<?php

namespace App\Entity;

use App\Repository\UserExtendedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserExtendedRepository::class)]
class UserExtended
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[ORM\JoinColumn(nullable: false)]
    private $birthday;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $address;

    #[ORM\Column(type: 'integer')]
    #[ORM\JoinColumn(nullable: false)]
    private $gender;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $region;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $yearEntry;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $yearExit;

    #[ORM\Column(type: 'integer')]
    private $nbAbscence;

    #[ORM\Column(type: 'boolean')]
    #[ORM\JoinColumn(nullable: false)]
    private $isStudent;

    #[ORM\Column(type: 'boolean')]
    #[ORM\JoinColumn(nullable: false)]
    private $hasProContract;

    #[ORM\Column(type: 'boolean')]
    #[ORM\JoinColumn(nullable: false)]
    private $isHired;

    #[ORM\OneToOne(inversedBy: 'userExtended', targetEntity: company::class, cascade: ['persist', 'remove'])]
    private $companyHired;

    #[ORM\OneToOne(inversedBy: 'userExtended', targetEntity: speciality::class, cascade: ['persist', 'remove'])]
    private $speciality_id;

    #[ORM\OneToOne(inversedBy: 'userExtended', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $user_id;

    #[ORM\OneToOne(inversedBy: 'userExtendedActualLevel', targetEntity: studyLevel::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $actualLevel;

    #[ORM\OneToOne(inversedBy: 'userExtendedPreviousLevel', targetEntity: studyLevel::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $previousLevel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getYearEntry(): ?string
    {
        return $this->yearEntry;
    }

    public function setYearEntry(string $yearEntry): self
    {
        $this->yearEntry = $yearEntry;

        return $this;
    }

    public function getYearExit(): ?string
    {
        return $this->yearExit;
    }

    public function setYearExit(string $yearExit): self
    {
        $this->yearExit = $yearExit;

        return $this;
    }

    public function getNbAbscence(): ?int
    {
        return $this->nbAbscence;
    }

    public function setNbAbscence(int $nbAbscence): self
    {
        $this->nbAbscence = $nbAbscence;

        return $this;
    }

    public function getIsStudent(): ?bool
    {
        return $this->isStudent;
    }

    public function setIsStudent(bool $isStudent): self
    {
        $this->isStudent = $isStudent;

        return $this;
    }

    public function getHasProContract(): ?bool
    {
        return $this->hasProContract;
    }

    public function setHasProContract(bool $hasProContract): self
    {
        $this->hasProContract = $hasProContract;

        return $this;
    }

    public function getIsHired(): ?bool
    {
        return $this->isHired;
    }

    public function setIsHired(bool $isHired): self
    {
        $this->isHired = $isHired;

        return $this;
    }

    public function getCompanyHired(): ?company
    {
        return $this->companyHired;
    }

    public function setCompanyHired(?company $companyHired): self
    {
        $this->companyHired = $companyHired;

        return $this;
    }

    public function getSpecialityId(): ?speciality
    {
        return $this->speciality_id;
    }

    public function setSpecialityId(?speciality $speciality_id): self
    {
        $this->speciality_id = $speciality_id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getActualLevel(): ?studyLevel
    {
        return $this->actualLevel;
    }

    public function setActualLevel(studyLevel $actualLevel): self
    {
        $this->actualLevel = $actualLevel;

        return $this;
    }

    public function getPreviousLevel(): ?studyLevel
    {
        return $this->previousLevel;
    }

    public function setPreviousLevel(studyLevel $previousLevel): self
    {
        $this->previousLevel = $previousLevel;

        return $this;
    }
}
