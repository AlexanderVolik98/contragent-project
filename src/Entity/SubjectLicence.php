<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subject_licence')]
class SubjectLicence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $series = null;

    #[ORM\Column(type: 'string')]
    private string $number;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $issueDate;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $issueAuthority;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $suspendDate = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $suspendAuthority = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $validFrom;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $validTo = null;

    // Сохраним перечень видов деятельности в виде JSON-массива.
    #[ORM\Column(type: 'json')]
    private array $activities = [];

    // Адреса (если будут) тоже храним как JSON, поле может быть null.
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $addresses = null;

    // Связь один к одному с сущностью Individual (ИП)
    #[ORM\OneToOne(targetEntity: Individual::class, inversedBy: 'licence')]
    #[ORM\JoinColumn(name: 'individual_id', referencedColumnName: 'id', nullable: true)]
    private ?Individual $individual = null;

    // Связь один к одному с сущностью Company (Компания)
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'licenses')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true)]
    private ?Company $company = null;

    // --- Геттеры и сеттеры ---
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(?string $series): self
    {
        $this->series = $series;
        return $this;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(?\DateTimeInterface $issueDate): self
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function getIssueAuthority(): ?string
    {
        return $this->issueAuthority;
    }

    public function setIssueAuthority(?string $issueAuthority): self
    {
        $this->issueAuthority = $issueAuthority;
        return $this;
    }

    public function getSuspendDate(): ?\DateTimeInterface
    {
        return $this->suspendDate;
    }

    public function setSuspendDate(?\DateTimeInterface $suspendDate): self
    {
        $this->suspendDate = $suspendDate;
        return $this;
    }

    public function getSuspendAuthority(): ?string
    {
        return $this->suspendAuthority;
    }

    public function setSuspendAuthority(?string $suspendAuthority): self
    {
        $this->suspendAuthority = $suspendAuthority;
        return $this;
    }

    public function getValidFrom(): \DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeInterface $validFrom): self
    {
        $this->validFrom = $validFrom;
        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;
        return $this;
    }

    public function getActivities(): array
    {
        return $this->activities;
    }

    public function setActivities(array $activities): self
    {
        $this->activities = $activities;
        return $this;
    }

    public function getAddresses(): ?array
    {
        return $this->addresses;
    }

    public function setAddresses(?array $addresses): self
    {
        $this->addresses = $addresses;
        return $this;
    }

    public function getIndividual(): ?Individual
    {
        return $this->individual;
    }

    public function setIndividual(?Individual $individual): self
    {
        $this->individual = $individual;
        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;
        return $this;
    }
}
