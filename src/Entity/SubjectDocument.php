<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity]
#[ORM\Table(name: 'subject_document')]
class SubjectDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $series = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $issueDate = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $issueAuthority = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true)]
    private ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: Individual::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(name: 'individual_id', referencedColumnName: 'id', nullable: true)]
    private ?Individual $individual = null;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getIssueDate(): ?DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function setIssueDate(?DateTimeImmutable $issueDate): self
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        if ($company !== null && $this->individual !== null) {
            throw new \InvalidArgumentException('Документ не может быть привязан одновременно к компании и ИП.');
        }
        $this->company = $company;

        return $this;
    }

    public function getIndividual(): ?Individual
    {
        return $this->individual;
    }

    public function setIndividual(?Individual $individual): self
    {
        if ($individual !== null && $this->company !== null) {
            throw new \InvalidArgumentException('Документ не может быть привязан одновременно к компании и ИП.');
        }
        $this->individual = $individual;

        return $this;
    }
}
