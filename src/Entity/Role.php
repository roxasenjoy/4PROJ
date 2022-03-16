<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $name;

    #[ORM\Column(type: 'integer')]
    #[ORM\JoinColumn(nullable: false)]
    private $permission;

    // TODO - A VERIFIER CAR FAIT A LA MAIN 
    #[ORM\OneToOne(inversedBy: 'campus', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $users;

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

    public function getPermission(): ?int
    {
        return $this->permission;
    }

    public function setPermission(int $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    // TODO - A VERIFIER CAR FAIT A LA MAIN 
    public function getUser(): ?User
    {
        return $this->user;
    }

    // TODO - A VERIFIER CAR FAIT A LA MAIN 
    public function getUsers(): ?User
    {
        return $this->users;
    }

    // TODO - A VERIFIER CAR FAIT A LA MAIN 
    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }
}
