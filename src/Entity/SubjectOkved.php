<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity]
#[ORM\Table(name: 'subject_okved', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_okved_subject', columns: ['okved_id', 'company_id', 'individual_id'])
])]
class SubjectOkved
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'companyOkveds')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Company $company;

    #[ORM\ManyToOne(targetEntity: Individual::class, inversedBy: 'individualOkveds')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Individual $individual = null;

    #[ORM\ManyToOne(targetEntity: Okved::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'companyOkveds')]
    #[ORM\JoinColumn(referencedColumnName: 'code', nullable: false)]
    private Okved $okved;

    #[ORM\Column(type: 'boolean')]
    private bool $isPrimary;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

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

    public function getOkved(): Okved
    {
        return $this->okved;
    }

    public function setOkved(Okved $okved): self
    {
        $this->okved = $okved;

        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;

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
}