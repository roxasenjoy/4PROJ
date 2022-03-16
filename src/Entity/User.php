<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[ORM\JoinColumn(nullable: false)]
    private $email;

    #[ORM\Column(type: 'string')]
    #[ORM\JoinColumn(nullable: false)]
    private $password;

    #[ORM\Column(type: 'string', length: 45)]
    #[ORM\JoinColumn(nullable: false)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 65)]
    #[ORM\JoinColumn(nullable: false)]
    private $lastName;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Defense::class)]
    private $defenses;

    #[ORM\OneToOne(mappedBy: 'users', targetEntity: Campus::class, cascade: ['persist', 'remove'])]
    private $campus;

    // TODO - A VERIFIER CAR FAIT A LA MAIN 
    #[ORM\OneToOne(mappedBy: 'users', targetEntity: Role::class, cascade: ['persist', 'remove'])]
    private $role;

    #[ORM\OneToOne(mappedBy: 'user_id', targetEntity: UserComptability::class, cascade: ['persist', 'remove'])]
    private $userComptability;

    #[ORM\OneToOne(mappedBy: 'user_id', targetEntity: UserGrade::class, cascade: ['persist', 'remove'])]
    private $userGrade;

    #[ORM\OneToOne(mappedBy: 'user_id', targetEntity: Intervenant::class, cascade: ['persist', 'remove'])]
    private $intervenant;

    #[ORM\OneToOne(mappedBy: 'user_id', targetEntity: UserExtended::class, cascade: ['persist', 'remove'])]
    private $userExtended;

    public function __construct()
    {
        $this->defenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Defense>
     */
    public function getDefenses(): Collection
    {
        return $this->defenses;
    }

    public function addDefense(Defense $defense): self
    {
        if (!$this->defenses->contains($defense)) {
            $this->defenses[] = $defense;
            $defense->setUserId($this);
        }

        return $this;
    }

    public function removeDefense(Defense $defense): self
    {
        if ($this->defenses->removeElement($defense)) {
            // set the owning side to null (unless already changed)
            if ($defense->getUserId() === $this) {
                $defense->setUserId(null);
            }
        }

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        // unset the owning side of the relation if necessary
        if ($campus === null && $this->campus !== null) {
            $this->campus->setUsers(null);
        }

        // set the owning side of the relation if necessary
        if ($campus !== null && $campus->getUsers() !== $this) {
            $campus->setUsers($this);
        }

        $this->campus = $campus;

        return $this;
    }

    public function getRoles(): ?Role
    {
        return $this->role;
    }

    public function setRoles(?Role $role): self
    {
        // unset the owning side of the relation if necessary
        if ($role === null && $this->role !== null) {
            $this->role->setUsers(null);
        }

        // set the owning side of the relation if necessary
        if ($role !== null && $role->getUsers() !== $this) {
            $role->setUsers($this);
        }

        $this->role = $role;

        return $this;
    }

    public function getUserComptability(): ?UserComptability
    {
        return $this->userComptability;
    }

    public function setUserComptability(?UserComptability $userComptability): self
    {
        // unset the owning side of the relation if necessary
        if ($userComptability === null && $this->userComptability !== null) {
            $this->userComptability->setUserId(null);
        }

        // set the owning side of the relation if necessary
        if ($userComptability !== null && $userComptability->getUserId() !== $this) {
            $userComptability->setUserId($this);
        }

        $this->userComptability = $userComptability;

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
            $this->userGrade->setUserId(null);
        }

        // set the owning side of the relation if necessary
        if ($userGrade !== null && $userGrade->getUserId() !== $this) {
            $userGrade->setUserId($this);
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
        // unset the owning side of the relation if necessary
        if ($intervenant === null && $this->intervenant !== null) {
            $this->intervenant->setUserId(null);
        }

        // set the owning side of the relation if necessary
        if ($intervenant !== null && $intervenant->getUserId() !== $this) {
            $intervenant->setUserId($this);
        }

        $this->intervenant = $intervenant;

        return $this;
    }

    public function getUserExtended(): ?UserExtended
    {
        return $this->userExtended;
    }

    public function setUserExtended(UserExtended $userExtended): self
    {
        // set the owning side of the relation if necessary
        if ($userExtended->getUserId() !== $this) {
            $userExtended->setUserId($this);
        }

        $this->userExtended = $userExtended;

        return $this;
    }
}
