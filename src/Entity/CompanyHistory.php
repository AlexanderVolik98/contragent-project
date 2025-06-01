<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "company_history")]
class CompanyHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Company",cascade: ["persist"], inversedBy: "history")]
    #[ORM\JoinColumn(name: "company_id", referencedColumnName: "id", nullable: false)]
    private ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Company", cascade: ["persist"])]
    #[ORM\JoinColumn(name: "successor_company_id", referencedColumnName: "id", nullable: true)]
    private ?Company $successorCompany = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Company", cascade: ["persist"])]
    #[ORM\JoinColumn(name: "predecessor_company_id", referencedColumnName: "id", nullable: true)]
    private ?Company $predecessorCompany = null;

    public function getId(): ?int
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

    public function getSuccessorCompany(): ?Company
    {
        return $this->successorCompany;
    }

    public function setSuccessorCompany(?Company $successorCompany): self
    {
        $this->successorCompany = $successorCompany;
        return $this;
    }

    public function getPredecessorCompany(): ?Company
    {
        return $this->predecessorCompany;
    }

    public function setPredecessorCompany(?Company $predecessorCompany): self
    {
        $this->predecessorCompany = $predecessorCompany;
        return $this;
    }
}
