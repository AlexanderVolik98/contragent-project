<?php

namespace App\Entity;

use App\Enum\ManagerTypeEnum;
use App\Repository\ManagerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: ManagerRepository::class)]
#[ORM\Table(name: 'company_manager')]
class CompanyManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private ?string $inn;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Company::class, fetch: 'EAGER', inversedBy: 'managers')]
    private ?Company $managedCompany = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $post = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $dadataId;

    #[ORM\Column(nullable: true)]
    private ?array $invalidity = null;

    #[ORM\Column(enumType: ManagerTypeEnum::class)]
    private ?ManagerTypeEnum $type;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\ManyToOne(targetEntity: Individual::class, cascade: ['persist'], inversedBy: 'managedCompanies')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Individual $individualManager = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'managerRole')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Company $companyManager = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function setInn(?string $inn): static
    {
        $this->inn = $inn;
        return $this;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function setPost(?string $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getDadataId(): string
    {
        return $this->dadataId;
    }

    public function setDadataId(string $dadataId): static
    {
        $this->dadataId = $dadataId;

        return $this;
    }

    public function getType(): ?ManagerTypeEnum
    {
        return $this->type;
    }

    public function setType(?ManagerTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getInvalidity(): ?array
    {
        return $this->invalidity;
    }

    public function setInvalidity(?array $invalidity): static
    {
        $this->invalidity = $invalidity;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function setManagedCompany(?Company $managedCompany): static
    {
        $this->managedCompany = $managedCompany;

        return $this;
    }

    public function getManagedCompany(): ?Company
    {
        return $this->managedCompany;
    }

    public function getIndividualManager(): ?Individual
    {
        return $this->individualManager;
    }

    public function setIndividualManager(?Individual $individualManager): self
    {
        $this->individualManager = $individualManager;

        return $this;
    }

    public function getCompanyManager(): ?Company
    {
        return $this->companyManager;
    }

    public function setCompanyManager(Company $companyManager): self
    {
        $this->companyManager = $companyManager;

        return $this;
    }
}