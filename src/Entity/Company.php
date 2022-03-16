<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[ORM\JoinColumn(nullable: false)]
    private $name;

    #[ORM\OneToOne(mappedBy: 'company_id', targetEntity: Partnership::class, cascade: ['persist', 'remove'])]
    private $partnership;

    #[ORM\OneToOne(mappedBy: 'companyHired', targetEntity: UserExtended::class, cascade: ['persist', 'remove'])]
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

    public function getPartnership(): ?Partnership
    {
        return $this->partnership;
    }

    public function setPartnership(Partnership $partnership): self
    {
        // set the owning side of the relation if necessary
        if ($partnership->getCompanyId() !== $this) {
            $partnership->setCompanyId($this);
        }

        $this->partnership = $partnership;

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
            $this->userExtended->setCompanyHired(null);
        }

        // set the owning side of the relation if necessary
        if ($userExtended !== null && $userExtended->getCompanyHired() !== $this) {
            $userExtended->setCompanyHired($this);
        }

        $this->userExtended = $userExtended;

        return $this;
    }
}
