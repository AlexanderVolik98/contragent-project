<?php

namespace App\Entity;

use App\Enum\FounderTypeEnum;
use App\Helper\OwnershipHelper;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity]
class Founder implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 510, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(enumType: FounderTypeEnum::class)]
    private FounderTypeEnum $type;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dadataId = null;

    #[ORM\ManyToOne(
        targetEntity: Company::class,
        fetch: 'EAGER',
        inversedBy: 'founders'
    )]
    #[ORM\JoinColumn(nullable: true)]
    #[Ignore]
    private ?Company $company = null;

    #[Ignore]
    #[ORM\ManyToOne(
        targetEntity: Individual::class,
        fetch: 'EAGER',
        inversedBy: 'founderProfile'
    )]
    #[ORM\JoinColumn(nullable: true)]
    private ?Individual $individual = null;

    public function getCompany(): ?Company
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

    #[ORM\ManyToOne(
        targetEntity: Company::class,
        cascade: ['persist'],
        inversedBy: 'foundedBy'
    )]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?Company $foundedCompany = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $share = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shareType = null;

    #[ORM\Column(nullable: true)]
    private ?array $invalidity = null;

    #[ORM\Column(nullable: true)]
    private ?array $data = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeInterface $startDate = null;

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

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): FounderTypeEnum
    {
        return $this->type;
    }

    public function setType(FounderTypeEnum $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getDadataId(): ?string
    {
        return $this->dadataId;
    }

    public function setDadataId(?string $dadataId): static
    {
        $this->dadataId = $dadataId;
        return $this;
    }

    public function setInvalidity(?array $invalidity): static
    {
        $this->invalidity = $invalidity;
        return $this;
    }

    public function getInvalidity(): ?array
    {
        return $this->invalidity;
    }

    public function getShare(): ?array
    {
        return $this->share;
    }

    public function setShare(?array $share): self
    {
        $this->share = $share;

        return $this;
    }

    public function getShareType(): ?string
    {
        return $this->shareType;
    }

    public function setShareType(?string $shareType): static
    {
        $this->shareType = $shareType;
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

    public function getFounderCompany(): ?Company
    {
        return $this->company;
    }

    public function setFounderCompany(?Company $company): static
    {
        $this->company = $company;
        return $this;
    }

    public function getFounderIndividual(): ?Individual
    {
        return $this->individual;
    }

    public function setFounderIndividual(?Individual $individual): static
    {
        $this->individual = $individual;

        return $this;
    }

    public function getFoundedCompany(): ?Company
    {
        return $this->foundedCompany;
    }

    public function setFoundedCompany(?Company $foundedCompany): static
    {
        $this->foundedCompany = $foundedCompany;

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

    #[\Override] public function jsonSerialize(): mixed
    {
        $shares = OwnershipHelper::calculateAllFractions($this->foundedCompany->getFoundedBy()->toArray());

        $individual = $this->individual;
        return [
            'id' => $this->id,
            'share' => $shares[$this->id],
            'name' => $this->company ? $this->company->getName() :
                $individual->getSurname() . ' ' . $individual->getName() . ' ' . $individual->getPatronymic(),
            'slug' => $this->company ? $this->company->getSlug() : $this->individual->getSlug(),
        ];
    }
}
