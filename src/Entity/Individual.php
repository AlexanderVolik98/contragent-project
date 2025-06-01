<?php

namespace App\Entity;

use App\Helper\DateHelper;
use App\Repository\IndividualRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndividualRepository::class)]
class Individual implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $inn = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $okpo = null;

    #[ORM\Column(length: 255, unique: false, nullable: true)]
    private ?string $dadataId = null;

    #[ORM\Column(type: "datetime_immutable", options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: "datetime_immutable", options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $updatedAt;

    public function getDadataId(): ?string
    {
        return $this->dadataId;
    }

    public function setDadataId(?string $dadataId): self
    {
        $this->dadataId = $dadataId;

        return $this;
    }

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $ogrnip = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $okato = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $oktmo = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $okogu = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $okfs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $patronymic = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(nullable: true)]
    private ?array $data = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $phones = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $emails = [];

    #[ORM\OneToOne(targetEntity: CompanyManager::class, mappedBy: 'individualManager')]
    private ?CompanyManager $managerRole = null;

    public function getPhones(): ?array
    {
        return $this->phones;
    }

    public function setPhones(?array $phones): self
    {
        $this->phones = $phones;

        return $this;
    }

    public function prePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getEmails(): ?array
    {
        return $this->emails;
    }

    public function setEmails(?array $emails): self
    {
        $this->emails = $emails;

        return $this;
    }

    #[ORM\OneToMany(
        targetEntity: SubjectOkved::class,
        mappedBy: 'individual',
        cascade: ['persist', 'remove'], // добавили cascade "persist"
        fetch: 'EAGER'
    )]
    private Collection $individualOkveds;

    #[ORM\OneToOne(
        targetEntity: SubjectFinance::class,
        mappedBy: 'individual',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    private ?SubjectFinance $finance = null;

    #[ORM\OneToOne(
        targetEntity: SubjectAddress::class,
        mappedBy: 'individual',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true,
    )]
    private ?SubjectAddress $address = null;

    #[ORM\OneToMany(
        targetEntity: SubjectDocument::class,
        mappedBy: 'individual',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER'
    )]
    private Collection $documents;

    #[ORM\OneToOne(
        targetEntity: SubjectState::class,
        mappedBy: 'individual',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    private ?SubjectState $state = null;

    #[ORM\OneToMany(targetEntity: Founder::class, mappedBy: 'individual')]
    private Collection $founderProfile;

    #[ORM\OneToMany(
        targetEntity: SubjectAuthority::class,
        mappedBy: 'individual',
        cascade: ['persist', 'remove'],
    )]
    private Collection $authorities;

    #[ORM\OneToMany(
        targetEntity: CompanyManager::class,
        mappedBy: 'individualManager',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
    )]
    private Collection $managedCompanies;

    const string INDIVIDUAL_TYPE = 'individual';

    public function __construct()
    {
        $this->individualOkveds = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->authorities = new ArrayCollection();
        $this->managedCompanies = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function getType(): string
    {
        return 'individual';
    }

    public function setPatronymic(?string $patronymic): static
    {
        $this->patronymic = $patronymic;

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

    public function getFinance(): ?SubjectFinance
    {
        return $this->finance;
    }

    public function setFinance(?SubjectFinance $finance): void
    {
        $finance->setIndividual($this);

        $this->finance = $finance;
    }

    public function getOgrnip(): ?string
    {
        return $this->ogrnip;
    }

    public function setOgrnip(?string $ogrnip): self
    {
        $this->ogrnip = $ogrnip;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getManagedCompanies(): Collection
    {
        return $this->managedCompanies;
    }

    public function getTaxService(): ?SubjectAuthority
    {
        $txService = $this->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'FEDERAL_TAX_SERVICE'
        );

        if ($txService->count() <= 0) {
            return null;
        }


        return $txService->current();
    }


    public function getPensionFond(): ?SubjectAuthority
    {
        $txService = $this->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'PENSION_FUND'
        );

        if ($txService->count() <= 0) {
            return null;
        }

        return $txService->current();
    }

    public function getTaxReport(): ?SubjectDocument
    {
        $txService = $this->documents->filter(
            fn(SubjectDocument $authority) =>
                $authority->getType() === 'FTS_REPORT'
        );

        if ($txService->count() <= 0) {
            return null;
        }

        return $txService->current();
    }

    public function getSocialInsuranceFond(): ?SubjectAuthority
    {
        $txService = $this->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'SOCIAL_INSURANCE_FUND'
        );

        if ($txService->count() <= 0) {
            return null;
        }

        return $txService->current();
    }

    public function getSocialInsuranceFondRegistration(): ?SubjectDocument
    {
        $txService = $this->documents->filter(
            fn(SubjectDocument $authority) =>
                $authority->getType() === 'SIF_REGISTRATION'
        );

        if ($txService->count() <= 0) {
            return null;
        }

        return $txService->current();
    }

    public function getPensionFondRegistration(): ?SubjectDocument
    {
        $txService = $this->documents->filter(
            fn(SubjectDocument $authority) =>
                $authority->getType() === 'PF_REGISTRATION'
        );

        if ($txService->count() <= 0) {
            return null;
        }

        return $txService->current();
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAddress(): ?SubjectAddress
    {
        return $this->address;
    }

    public function setAddress(?SubjectAddress $address): static
    {
        $address->setIndividual($this);

        $this->address = $address;

        return $this;
    }

    public function getState(): ?SubjectState
    {
        return $this->state;
    }

    public function setState(?SubjectState $state): static
    {
        $this->state = $state;

        $state->setIndividual($this);

        return $this;
    }

    public function getOkpo(): ?string
    {
        return $this->okpo;
    }

    public function setOkpo(?string $okpo): self
    {
        $this->okpo = $okpo;

        return $this;
    }

    public function getOkato(): ?string
    {
        return $this->okato;
    }

    public function setOkato(?string $okato): self
    {
        $this->okato = $okato;

        return $this;
    }

    public function getOktmo(): ?string
    {
        return $this->oktmo;
    }

    public function setOktmo(?string $oktmo): self
    {
        $this->oktmo = $oktmo;

        return $this;
    }

    public function getOkogu(): ?string
    {
        return $this->okogu;
    }

    public function setOkogu(?string $okogu): self
    {
        $this->okogu = $okogu;

        return $this;
    }

    public function getOkfs(): ?string
    {
        return $this->okfs;
    }

    public function setOkfs(?string $okfs): self
    {
        $this->okfs = $okfs;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getOkved(): ?Okved
    {
        return $this->individualOkveds->filter(
            fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === true
        )[0]?->getOkved();
    }

    public function getSecondaryOkveds(): ArrayCollection|Collection
    {
        return $this->individualOkveds->filter(
            fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === false
        )->map(fn(SubjectOkved $subjectOkved) => $subjectOkved->getOkved());
    }

    public function addOkved(SubjectOkved $subjectOkved): self
    {
        if (!$this->individualOkveds->contains($subjectOkved)) {
            $this->individualOkveds->add($subjectOkved);
            $subjectOkved->setIndividual($this);
        }

        return $this;
    }

    public function getIndividualOkveds(): Collection
    {
        return $this->individualOkveds;
    }

    #[\Override] public function jsonSerialize(): mixed
    {
        $mainOkved = $this->individualOkveds->filter(
            fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === true
        )[0]?->getOkved();

        if ($mainOkved) {
            $mainOkved = ['code' => $mainOkved->getCode(), 'name' => $mainOkved->getName()];
        } else {
            $mainOkved = null;
        }


        $secondaryOkveds = $this->getIndividualOkveds()
            ->filter(fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === false);

        $secondaryOkveds = $secondaryOkveds->map(fn(SubjectOkved $subjectOkved) => [
            'code' => $subjectOkved->getOkved()->getCode(),
            'name' => $subjectOkved->getOkved()->getName(),
        ])->toArray();


        $taxService = $this->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'FEDERAL_TAX_SERVICE'
        )->map(
            fn(SubjectAuthority $authority) =>
            ['name' => $authority->getName(), 'code' => $authority->getCode()]
        )->toArray();

        $taxReport = $this->documents->filter(
            fn(SubjectDocument $document) =>
                $document->getType() === 'FTS_REPORT'
        )->map(
            fn(SubjectDocument $document) =>
            ['type' => $document->getType(), 'issueDate' => DateHelper::formatDate($document->getIssueDate())]
        )->toArray();

        $pensionFond = $this->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'PENSION_FUND'
        )->map(
            fn(SubjectAuthority $authority) =>
            ['name' => $authority->getName(), 'code' => $authority->getCode()]
        )->toArray();

        $pensionFondRegistration = $this->documents->filter(
            fn(SubjectDocument $document) =>
                $document->getType() === 'PF_REGISTRATION'
        )->map(
            fn(SubjectDocument $document) =>
            ['type' => $document->getType(), 'issueDate' => DateHelper::formatDate($document->getIssueDate())]
        )->toArray();

        $socialInsuranceFond = $this->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'SOCIAL_INSURANCE_FUND'
        )->map(
            fn(SubjectAuthority $authority) =>
            ['name' => $authority->getName(), 'code' => $authority->getCode()]
        )->toArray();

        $socialInsuranceFondRegistration = $this->documents->filter(
            fn(SubjectDocument $document) =>
                $document->getType() === 'SIF_REGISTRATION'
        )->map(
            fn(SubjectDocument $document) =>
            ['type' => $document->getType(), 'issueDate' => DateHelper::formatDate($document->getIssueDate())]
        )->toArray();

        $taxReport = !empty($taxReport) ? array_values($taxReport)[0] : null;
        $socialInsuranceFond = !empty($socialInsuranceFond) ? array_values($socialInsuranceFond)[0] : null;
        $socialInsuranceFondRegistration = !empty($socialInsuranceFondRegistration) ? array_values($socialInsuranceFondRegistration)[0] : null;
        $pensionFond = !empty($pensionFond) ? array_values($pensionFond)[0] : null;
        $pensionFondRegistration = !empty($pensionFondRegistration) ? array_values($pensionFondRegistration)[0] : null;
        $taxService = !empty($taxService) ? array_values($taxService)[0] : null;

        if (null !== $this->state) {
            $status = [
                'name' => $this->state->getStatus(),
                'code' => $this->state->getCode(),
                'comment' => $this->state->getComment(),
                'liquidationDate' => $this->state->getLiquidationDate()?->format('d.m.Y'),
                'registrationDate' => $this->state->getRegistrationDate()?->format('d.m.Y'),
                'actuallyDate' => $this->state->getActualityDate()?->format('d.m.Y'),
            ];
        } else {
            $status = null;
        }

        return [
            'id' => $this->id,
            'inn' => $this->inn,
            'okpo' => $this->okpo,
            'okato' => $this->okato,
            'oktmo' => $this->oktmo,
            'ogrnip' => $this->ogrnip,
            'okogu' => $this->okogu,
            'okfs' => $this->okfs,
            'name' => $this->name,
            'taxService' => $taxService,
            'taxReport' => $taxReport,
            'socialInsuranceFond' => $socialInsuranceFond,
            'socialInsuranceFondRegistration' => $socialInsuranceFondRegistration,
            'pensionFond' => $pensionFond,
            'pensionFondRegistration' => $pensionFondRegistration,
            'type' => self::INDIVIDUAL_TYPE,
            'slug' => $this->slug,
            'surname' => $this->surname,
            'patronymic' => $this->patronymic,
            'gender' => $this->gender,
            $status,
            'managedCompanies' => $this->getManagedCompaniesData(),
            'address' => $this->address?->getFullAddress(), // Проверяем null
            'finance' => $this->finance ? [
                'income' => $this->finance->getIncome(),
            ] : null, // Только нужные поля
            'phones' => $this->phones,
            'emails' => $this->emails,
            'okved' => $mainOkved,
            'secondaryOkveds' => $secondaryOkveds,
            'documents' => $this->documents->map(fn($doc) => [
                'type' => $doc->getType(),
                'number' => $doc->getNumber(),
            ])->toArray(),
            'authorities' => $this->authorities->map(fn($auth) => $auth->getId())->toArray(), // Только ID
        ];
    }

    public function getManagedCompaniesData(): array
    {
        return $this->managedCompanies
            ->map(fn(CompanyManager $cm) => [
                'inn' => $cm->getManagedCompany()?->getInn(),
                'name' => $cm->getManagedCompany()?->getOpf()->getAbbreviation() . ' ' . $cm->getManagedCompany()?->getName(),
                'slug' => $cm->getManagedCompany()?->getSlug(),
                'post' => $cm->getPost(),
                'startDate' => $cm->getStartDate()?->format('d.m.Y'),
            ])
            ->toArray();
    }

    public function addDocument(SubjectDocument $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setIndividual($this);
        }

        return $this;
    }

    public function addAuthority(SubjectAuthority $authority): self
    {
        if (!$this->authorities->contains($authority)) {
            $this->authorities->add($authority);
            $authority->setIndividual($this);
        }

        return $this;
    }
}
