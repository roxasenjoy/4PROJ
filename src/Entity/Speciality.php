<?php

namespace App\Entity;

use App\Repository\SpecialityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialityRepository::class)]
class Speciality
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private $name;

    #[ORM\OneToOne(mappedBy: 'speciality_id', targetEntity: UserExtended::class, cascade: ['persist', 'remove'])]
    private $userExtended;

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

    public function getUserExtended(): ?UserExtended
    {
        return $this->userExtended;
    }

    public function setUserExtended(?UserExtended $userExtended): self
    {
        // unset the owning side of the relation if necessary
        if ($userExtended === null && $this->userExtended !== null) {
            $this->userExtended->setSpecialityId(null);
        }

        // set the owning side of the relation if necessary
        if ($userExtended !== null && $userExtended->getSpecialityId() !== $this) {
            $userExtended->setSpecialityId($this);
        }

        $this->userExtended = $userExtended;

        return $this;
    }
}
