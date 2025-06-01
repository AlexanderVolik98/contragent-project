<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\SubjectStatusEnum;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity]
class SubjectState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(nullable: true, enumType: SubjectStatusEnum::class)]
    private ?SubjectStatusEnum $status;

    #[Ignore]
    #[ORM\OneToOne(targetEntity: Company::class, inversedBy: 'state')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true)]
    private Company $company;

    #[Ignore]
    #[ORM\OneToOne(targetEntity: Individual::class, inversedBy: 'state')]
    #[ORM\JoinColumn(name: 'individual_id', referencedColumnName: 'id', nullable: true)]
    private ?Individual $individual = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $actualityDate;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $registrationDate;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $liquidationDate = null;

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): ?SubjectStatusEnum
    {
        return $this->status;
    }

    public function setStatus(?SubjectStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getIndividual(): ?Individual
    {
        return $this->individual;
    }

    public function setIndividual(?Individual $individual): void
    {
        $this->individual = $individual;
    }

    public function getActualityDate(): \DateTimeImmutable
    {
        return $this->actualityDate;
    }

    public function setActualityDate(\DateTimeImmutable $actualityDate): self
    {
        $this->actualityDate = $actualityDate;

        return $this;
    }

    public function getRegistrationDate(): \DateTimeImmutable
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeImmutable $registrationDate): self
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    public function getLiquidationDate(): ?\DateTimeImmutable
    {
        return $this->liquidationDate;
    }

    public function setLiquidationDate(?\DateTimeImmutable $liquidationDate): self
    {
        $this->liquidationDate = $liquidationDate;
        return $this;
    }
}
