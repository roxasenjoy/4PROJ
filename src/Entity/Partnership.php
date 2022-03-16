<?php

namespace App\Entity;

use App\Repository\PartnershipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnershipRepository::class)]
class Partnership
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

    #[ORM\OneToOne(inversedBy: 'partnership', targetEntity: company::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $company_id;

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

    public function getCompanyId(): ?company
    {
        return $this->company_id;
    }

    public function setCompanyId(company $company_id): self
    {
        $this->company_id = $company_id;

        return $this;
    }
}
