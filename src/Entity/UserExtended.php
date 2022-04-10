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
    private $birthday;

    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    #[ORM\Column(type: 'integer')]
    private $gender;

    #[ORM\Column(type: 'string', length: 255)]
    private $region;

    #[ORM\Column(type: 'string', length: 255)]
    private $yearEntry;

    #[ORM\Column(type: 'string', length: 255)]
    private $yearExit;

    #[ORM\Column(type: 'integer')]
    private $nbAbscence;

    #[ORM\Column(type: 'boolean')]
    private $isStudent;

    #[ORM\Column(type: 'boolean')]
    private $hasProContract;

    #[ORM\Column(type: 'boolean')]
    private $isHired;

    #[ORM\ManyToOne(targetEntity: StudyLevel::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private $actualLevel;

    #[ORM\ManyToOne(targetEntity: StudyLevel::class, fetch: 'EAGER')]
    private $previousLevel;

    #[ORM\OneToOne(inversedBy: 'userExtended', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

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

    public function getActualLevel(): ?StudyLevel
    {
        return $this->actualLevel;
    }

    public function setActualLevel(?StudyLevel $actualLevel): self
    {
        $this->actualLevel = $actualLevel;

        return $this;
    }

    public function getPreviousLevel(): ?StudyLevel
    {
        return $this->previousLevel;
    }

    public function setPreviousLevel(?StudyLevel $previousLevel): self
    {
        $this->previousLevel = $previousLevel;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(user $user): self
    {
        $this->user = $user;

        return $this;
    }
}
