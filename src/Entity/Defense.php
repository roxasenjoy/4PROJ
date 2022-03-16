<?php

namespace App\Entity;

use App\Repository\DefenseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefenseRepository::class)]
class Defense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[ORM\JoinColumn(nullable: false)]
    private $dateBegin;

    #[ORM\Column(type: 'datetime')]
    #[ORM\JoinColumn(nullable: false)]
    private $dateEnd;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'defenses')]
    #[ORM\JoinColumn(nullable: false)]
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->dateBegin;
    }

    public function setDateBegin(\DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

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
}
