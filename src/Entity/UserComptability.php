<?php

namespace App\Entity;

use App\Repository\UserComptabilityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserComptabilityRepository::class)]
class UserComptability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 45)]
    #[ORM\JoinColumn(nullable: false)]
    private $type;

    #[ORM\Column(type: 'boolean')]
    private $paid;

    #[ORM\Column(type: 'integer')]
    private $payementDue;

    #[ORM\Column(type: 'boolean')]
    private $relance;

    #[ORM\OneToOne(targetEntity: user::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getPayementDue(): ?int
    {
        return $this->payementDue;
    }

    public function setPayementDue(int $payementDue): self
    {
        $this->payementDue = $payementDue;

        return $this;
    }

    public function getRelance(): ?bool
    {
        return $this->relance;
    }

    public function setRelance(bool $relance): self
    {
        $this->relance = $relance;

        return $this;
    }


}
