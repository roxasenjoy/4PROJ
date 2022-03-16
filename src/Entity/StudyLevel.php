<?php

namespace App\Entity;

use App\Repository\StudyLevelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudyLevelRepository::class)]
class StudyLevel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $name;

    #[ORM\OneToOne(mappedBy: 'actualLevel', targetEntity: UserExtended::class, cascade: ['persist', 'remove'])]
    private $userExtendedActualLevel;

    #[ORM\OneToOne(mappedBy: 'previousLevel', targetEntity: UserExtended::class, cascade: ['persist', 'remove'])]
    private $userExtendedPreviousLevel;

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

    public function getUserExtendedActualLevel(): ?UserExtended
    {
        return $this->userExtendedActualLevel;
    }

    public function setUserExtendedActualLevel(UserExtended $userExtendedActualLevel): self
    {
        // set the owning side of the relation if necessary
        if ($userExtendedActualLevel->getActualLevel() !== $this) {
            $userExtendedActualLevel->setActualLevel($this);
        }

        $this->userExtendedActualLevel = $userExtendedActualLevel;

        return $this;
    }

    public function getUserExtendedPreviousLevel(): ?UserExtended
    {
        return $this->userExtendedPreviousLevel;
    }

    public function setUserExtendedPreviousLevel(UserExtended $userExtendedPreviousLevel): self
    {
        // set the owning side of the relation if necessary
        if ($userExtendedPreviousLevel->getPreviousLevel() !== $this) {
            $userExtendedPreviousLevel->setPreviousLevel($this);
        }

        $this->userExtendedPreviousLevel = $userExtendedPreviousLevel;

        return $this;
    }

}
