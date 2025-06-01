<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subject_finance')]
class SubjectFinance
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToOne(targetEntity: Company::class, inversedBy: 'finance')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true)]
    private ?Company $company;

    #[ORM\OneToOne(targetEntity: Individual::class, inversedBy: 'finance')]
    #[ORM\JoinColumn(name: 'individual_id', referencedColumnName: 'id', nullable: true)]
    private ?Individual $individual;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $taxSystem;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $income;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $expense;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $revenue;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $debt = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $penalty = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $year;

    public function getId(): int
    {
        return $this->id;
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

    public function getIndividual(): ?Individual
    {
        return $this->individual;
    }

    public function setIndividual(?Individual $individual): self
    {
        $this->individual = $individual;

        return $this;
    }

    public function getTaxSystem(): ?string
    {
        return $this->taxSystem;
    }

    public function setTaxSystem(?string $taxSystem): self
    {
        $this->taxSystem = $taxSystem;

        return $this;
    }

    public function getIncome(): ?float
    {
        return $this->income;
    }

    public function setIncome(?float $income): self
    {
        $this->income = $income;

        return $this;
    }

    public function getExpense(): ?float
    {
        return $this->expense;
    }

    public function setExpense(?float $expense): self
    {
        $this->expense = $expense;

        return $this;
    }

    public function getRevenue(): ?float
    {
        return $this->revenue;
    }

    public function setRevenue(?float $revenue): self
    {
        $this->revenue = $revenue;

        return $this;
    }

    public function getDebt(): ?float
    {
        return $this->debt;
    }

    public function setDebt(?float $debt): self
    {
        $this->debt = $debt;
        return $this;
    }

    public function getPenalty(): ?float
    {
        return $this->penalty;
    }

    public function setPenalty(?float $penalty): self
    {
        $this->penalty = $penalty;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }
}
