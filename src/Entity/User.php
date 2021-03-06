<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ApiController;
use App\Controller\MeController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => [
            'controller' => NotFoundAction::class,
            'openapi_context' => ['summary' => 'hidden'],
            'read' => false,
            'output' => false
        ],

        'user' => [
            'pagination_enabled' => false,
            'path' => 'v1/user',
            'method' => 'get',
            'controller' => ApiController::class,
            'read' => false,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ]
        ],
    ],
    normalizationContext: ['groups' => ['read:User']]
)]

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Votre email est déjà utilisée.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['read:User'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['read:User'])]
    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;

    #[Groups(['read:User'])]
    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;

    #[Groups(['read:User'])]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[Groups(['read:User'])]
    #[ORM\Column(type: 'json')]
    private $roles = array();

    #[ORM\Column(type: 'string')]
    private $password;

    #[Groups(['read:User'])]
    #[ORM\ManyToOne(targetEntity: Campus::class, fetch:'EAGER', inversedBy: 'users')]
    private $campus;

    #[Groups(['read:User'])]
    #[ORM\ManyToOne(targetEntity: Role::class, fetch:'EAGER',  inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private $role;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserGrade::class, cascade: ['persist', 'remove'], fetch: 'EAGER')]
    private $userGrades;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserExtended::class, cascade: ['persist', 'remove'])]
    private $userExtended;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SubjectDate::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $subjectDates;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $idExtended;


    public function __construct()
    {
        $this->userGrades = new ArrayCollection();
        $this->subjectDates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

//        // guarantee every user at least has ROLE_USER
//        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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



    public function getRole(): ?role
    {
        return $this->role;
    }

    public function setRole(?role $role): self
    {
        $this->role = $role;

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
            $userGrade->setUser($this);
        }

        return $this;
    }

    public function removeUserGrade(UserGrade $userGrade): self
    {
        if ($this->userGrades->removeElement($userGrade)) {
            // set the owning side to null (unless already changed)
            if ($userGrade->getUser() === $this) {
                $userGrade->setUser(null);
            }
        }

        return $this;
    }

    public function getUserExtended(): ?UserExtended
    {
        return $this->userExtended;
    }

    public function setUserExtended(UserExtended $userExtended): self
    {
        // set the owning side of the relation if necessary
        if ($userExtended->getUser() !== $this) {
            $userExtended->setUser($this);
        }

        $this->userExtended = $userExtended;

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
            $subjectDate->setUser($this);
        }

        return $this;
    }

    public function removeSubjectDate(SubjectDate $subjectDate): self
    {
        if ($this->subjectDates->removeElement($subjectDate)) {
            // set the owning side to null (unless already changed)
            if ($subjectDate->getUser() === $this) {
                $subjectDate->setUser(null);
            }
        }

        return $this;
    }

    public static function createFromPayload($username, array $payload)
    {
        // TODO: Implement createFromPayload() method.

        $user = new User();

        $user->setId($username)->setEmail($payload['username'] ?? '');

        return $user;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getIdExtended(): ?string
    {
        return $this->idExtended;
    }

    public function setIdExtended(?string $idExtended): self
    {
        $this->idExtended = $idExtended;

        return $this;
    }
}
