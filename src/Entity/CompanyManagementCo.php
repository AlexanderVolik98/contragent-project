<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity]
class CompanyManagementCo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 510, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dadataId = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'managementCompanies')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Company $managedCompany = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'managerCompanies')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Company $managerCompany = null;

    #[ORM\Column(nullable: true)]
    private ?array $data = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDadataId(): ?string
    {
        return $this->dadataId;
    }

    public function setDadataId(?string $dadataId): self
    {
        $this->dadataId = $dadataId;

        return $this;
    }

    public function getManagedCompany(): ?Company
    {
        return $this->managedCompany;
    }

    public function setManagedCompany(?Company $managedCompany): self
    {
        $this->managedCompany = $managedCompany;

        return $this;
    }

    public function getManagerCompany(): ?Company
    {
        return $this->managerCompany;
    }

    public function setManagerCompany(?Company $managerCompany): self
    {
        $this->managerCompany = $managerCompany;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
