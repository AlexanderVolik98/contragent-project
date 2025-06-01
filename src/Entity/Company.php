<?php

namespace App\Entity;

use App\Helper\DateHelper;
use App\Helper\OwnershipHelper;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[UniqueEntity(fields: ['inn', 'kpp'])]
#[Index(name: "idx_dadata_id", columns: ["dadata_id"])]
#[Index(name: "idx_inn", fields: ["inn"])]
#[Index(name: "idx_kpp", fields: ["kpp"])]
#[Index(name: "idx_ogrn", fields: ["ogrn"])]
#[ORM\UniqueConstraint(name: 'uniq_inn_kpp', columns: ['inn', 'kpp'])]
class Company implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: false, nullable: true)]
    private ?string $dadataId = null;

    #[ORM\Column(type: "bigint", nullable: true)]
    private ?int $inn = null;

    #[ORM\Column(type: "bigint", nullable: true)]
    private ?int $kpp = null;

    #[ORM\Column]
    private ?bool $isMain = null;

    #[ORM\Column(nullable: false)]
    private bool $foundersProcessed = false;

    #[ORM\Column(nullable: false)]
    private bool $affiliatedProcessed = false;

    #[ORM\Column(nullable: false)]
    private bool $historyProcessed = false;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $managersProcessed = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_company_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?Company $parentCompany = null;

    #[Ignore]
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parentCompany', fetch: 'EAGER')]
    private Collection $children;

    #[ORM\Column(type: "bigint", nullable: true)]
    private ?int $ogrn = null;

    #[ORM\Column(length: 510)]
    private ?string $name = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $phones = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $emails = [];

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private ?string $slug = null;

    #[ORM\OneToOne(
        targetEntity: SubjectAddress::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private ?SubjectAddress $address = null;

    #[ORM\OneToOne(
        targetEntity: CompanyCapital::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private ?CompanyCapital $capital = null;

    #[ORM\OneToOne(
        targetEntity: SubjectFinance::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private ?SubjectFinance $finance = null;

    #[ORM\OneToMany(
        targetEntity: SubjectOkved::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
    )]
    private Collection $companyOkveds;

    #[Ignore]
    #[ORM\OneToMany(
        targetEntity: CompanyManagementCo::class,
        mappedBy: 'managedCompany',
        cascade: ['persist', 'remove'],
    )]
    private Collection $managerCompanies;

    #[Ignore]
    #[ORM\OneToMany(
        targetEntity: CompanyManagementCo::class,
        mappedBy: 'managerCompany',
        cascade: ['persist', 'remove'],
    )]
    private Collection $managedCompanies;

    #[ORM\OneToMany(
        targetEntity: CompanyManager::class,
        mappedBy: 'managedCompany',
        cascade: ['persist', 'remove'],
    )]
    private Collection $managers;

    #[ORM\ManyToOne(
        targetEntity: Opf::class,
        fetch: 'EAGER',
        inversedBy: 'companies'
    )]
    private ?Opf $opf = null;

    #[ORM\OneToMany(
        targetEntity: SubjectAuthority::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
    )]
    private Collection $authorities;

    #[ORM\OneToOne(
        targetEntity: SubjectState::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    private ?SubjectState $state = null;

    #[ORM\OneToMany(targetEntity: CompanyHistory::class, mappedBy: "successorCompany")]
    private Collection $successorHistories;

    #[ORM\OneToMany(targetEntity: CompanyHistory::class, mappedBy: "predecessorCompany")]
    private Collection $predecessorHistories;

    #[ORM\OneToMany(targetEntity: CompanyHistory::class, mappedBy: "company")]
    private Collection $history;

    #[ORM\Column(nullable: true)]
    private ?array $data = null;

    #[ORM\OneToMany(
        targetEntity: Founder::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
    )]
    private Collection $founders;

    #[ORM\OneToMany(
        targetEntity: Founder::class,
        mappedBy: 'foundedCompany',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER'
    )]
    private Collection $foundedBy;

    #[ORM\OneToMany(
        targetEntity: SubjectLicence::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    private Collection $licenses;

    #[ORM\OneToMany(
        targetEntity: SubjectDocument::class,
        mappedBy: 'company',
        cascade: ['persist', 'remove'],
    )]
    private Collection $documents;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $okpo = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $okato = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $oktmo = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $okogu = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $okfs = null;

    #[ORM\Column(type: "datetime_immutable", options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: "datetime_immutable", options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToOne(targetEntity: CompanyManager::class, mappedBy: 'companyManager')]
    private ?CompanyManager $managerRole = null;

    const string COMPANY_TYPE = 'company';

    public function __construct()
    {
        $this->companyOkveds = new ArrayCollection();
        $this->founders = new ArrayCollection();
        $this->managers = new ArrayCollection();
        $this->authorities = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->managerCompanies = new ArrayCollection();
        $this->managedCompanies = new ArrayCollection();
        $this->successorHistories = new ArrayCollection();
        $this->predecessorHistories = new ArrayCollection();
        $this->history = new ArrayCollection();
        $this->licenses = new ArrayCollection();
        $this->foundedBy = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getFoundedBy(): ArrayCollection|Collection
    {
        return $this->foundedBy;
    }

    public function getJsonFoundedBy(): ?array
    {
        $jsonFoundedBy = [];

        foreach ($this->getFoundedBy() as $item) {
            $jsonFoundedBy[] = json_encode($item);
        }

        return $jsonFoundedBy;
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

    // Пример метода обновления updatedAt перед сохранением
    public function prePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Метод для обновления поля updatedAt при обновлении записи
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Методы для createdAt и updatedAt
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function setManagers(Collection $managers): self
    {
        $this->managers = $managers;

        return $this;
    }

    public function getHistoryStatus(): string
    {
        return match (true) {
            !$this->data['successors'] && !$this->data['predecessors'] => 'empty',
            !$this->historyProcessed => 'processing',
            default => 'ok',
        };
    }

    public function getFoundersStatus(): string
    {
        return match (true) {
            !$this->data['founders'] => 'empty',
            !$this->foundersProcessed => 'processing',
            default => 'ok',
        };
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilials(): array
    {
        return $this->children->map(fn(Company $company) => [
            'name' => $company->getName(),
            'slug' => $company->getSlug(),
            'opf' => $company->getOpf(),
            'inn' => $company->getInn(),
            'kpp' => $company->getKpp(),
        ])->toArray();
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getPhones(): ?array
    {
        return $this->phones;
    }

    public function setPhones(?array $phones): self
    {
        $this->phones = $phones;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOpf(): ?Opf
    {
        return $this->opf;
    }

    public function setOpf(?Opf $opf): static
    {
        $this->opf = $opf;

        return $this;
    }

    public function getType(): string
    {
        return 'Company';
    }

    public function getInn(): ?int
    {
        return $this->inn;
    }

    public function setInn(?int $inn): static
    {
        $this->inn = $inn;

        return $this;
    }

    public function getKpp(): ?int
    {
        return $this->kpp;
    }

    public function setKpp(?int $kpp): static
    {
        $this->kpp = $kpp;

        return $this;
    }

    public function isMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(?bool $isMain): static
    {
        $this->isMain = $isMain;

        return $this;
    }

    public function getOgrn(): ?int
    {
        return $this->ogrn;
    }

    public function setOgrn(?int $ogrn): static
    {
        $this->ogrn = $ogrn;

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

    public function getFounders(): Collection
    {
        return $this->founders;
    }

    public function setFounders(Collection $founders): static
    {
        $this->founders = $founders;

        return $this;
    }

    public function getAddress(): ?SubjectAddress
    {
        return $this->address;
    }

    public function setAddress(?SubjectAddress $address): void
    {
        $address->setCompany($this);

        $this->address = $address;
    }

    public function getCapital(): ?CompanyCapital
    {
        return $this->capital;
    }

    public function setCapital(?CompanyCapital $capital): void
    {
        $this->capital = $capital;
    }

    public function getFinance(): ?SubjectFinance
    {
        return $this->finance;
    }

    public function setFinance(?SubjectFinance $finance): void
    {
        $finance->setCompany($this);

        $this->finance = $finance;
    }

    public function getState(): ?SubjectState
    {
        return $this->state;
    }

    public function setState(?SubjectState $state): static
    {
        $this->state = $state;

        $state->setCompany($this);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getSuccessorHistory(): Collection
    {
        return $this->successorHistories;
    }

    public function setSuccessorHistories(Collection $successorHistories): void
    {
        $this->successorHistories = $successorHistories;
    }

    public function getPredecessorHistory(): Collection
    {
        return $this->predecessorHistories;
    }

    public function setPredecessorHistory(Collection $predecessorHistories): void
    {
        $this->predecessorHistories = $predecessorHistories;
    }

    public function getParentCompany(): ?Company
    {
        return $this->parentCompany;
    }

    public function setParentCompany(?Company $parentCompany): self
    {
        $this->parentCompany = $parentCompany;

        return $this;
    }

    public function getSecondaryOkveds(): Collection
    {
        if ($this->getParentCompany() !== null) {
            return $this->getParentCompany()->getSecondaryOkveds();
        }

        return $this->getCompanyOkveds()
            ->filter(fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === false);
    }

    public function getPrimaryOkved(): SubjectOkved
    {
        if ($this->getParentCompany() !== null) {
            return $this->getParentCompany()->getPrimaryOkved();
        }

        return $this->getCompanyOkveds()
            ->filter(fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === true)->current();
    }

    public function getTaxService(): ?SubjectAuthority
    {
        if ($this->getParentCompany() !== null) {
            return $this->getParentCompany()->getTaxService();
        }

        return $this->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'FEDERAL_TAX_SERVICE'
        )->current();
    }

    public function getPensionFond(): ?SubjectAuthority
    {
        if ($this->getParentCompany() !== null) {
            return $this->getParentCompany()->getPensionFond();
        }

        return $this->authorities->filter(
            fn(SubjectAuthority $authority) => $authority->getType() === 'PENSION_FUND'
        )->current();
    }

    public function getTaxReport(): ?SubjectDocument
    {
        if ($this->getParentCompany() !== null) {
            return $this->getParentCompany()->getTaxReport();
        }

        return $this->documents->filter(
            fn(SubjectDocument $document) =>
                $document->getType() === 'FTS_REPORT'
        )->current();
    }

    public function getSocialInsuranceFond(): ?SubjectAuthority
    {
        if ($this->getParentCompany() !== null) {
            return $this->getParentCompany()->getSocialInsuranceFond();
        }

        return $this->authorities->filter(
            fn(SubjectAuthority $authority) => $authority->getType() === 'SOCIAL_INSURANCE_FUND'
        )->current();
    }

    public function getSocialInsuranceFondRegistration(): ?SubjectDocument
    {
        if ($this->getParentCompany() !== null) {
            return $this->getParentCompany()->getSocialInsuranceFondRegistration();
        }

        return $this->documents->filter(
            fn(SubjectDocument $document) => $document->getType() === 'SIF_REGISTRATION'
        )->current();
    }

    public function getPensionFondRegistration(): ?SubjectDocument
    {
        if ($this->getParentCompany() !== null) {
            return $this->getParentCompany()->getSocialInsuranceFondRegistration();
        }

//        $pensionFondReg = $this->documents->filter(
//            fn(SubjectDocument $document) =>
//                $document->getType() === 'PF_REGISTRATION'
//        )->map(
//            fn(SubjectDocument $document) =>
//            ['type' => $document->getType(), 'issueDate' => DateHelper::formatDate($document->getIssueDate())]
//        )->toArray();
//
//        return reset($pensionFondReg);

        return $this->documents->filter(
            fn(SubjectDocument $document) =>
                $document->getType() === 'PF_REGISTRATION'
        )->current();
    }

    /**
     * @return Collection<int, Company>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    #[\Override] public function jsonSerialize(): mixed
    {
        if ($this->getParentCompany() === null) {

            $entityForOkveds = $this;
            $primOkved = $entityForOkveds->getCompanyOkveds()
                ->filter(fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === true)
                ->get(0)?->getOkved();
        } else {
            $parentCompany = $this->getParentCompany();

            $entityForOkveds = $parentCompany;
            $primOkved = $entityForOkveds->getCompanyOkveds()
                ->filter(fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === true)
                ->get(0)?->getOkved();
        }

        $secondaryOkveds = $entityForOkveds->getCompanyOkveds()
            ->filter(fn(SubjectOkved $subjectOkved) => $subjectOkved->isPrimary() === false);

        $secondaryOkveds = $secondaryOkveds->map(fn(SubjectOkved $subjectOkved) => [
            'code' => $subjectOkved->getOkved()->getCode(),
            'name' => $subjectOkved->getOkved()->getName(),
        ])->toArray();

        if ($primOkved) {
            $primOkved = ['name' => $primOkved->getName(), 'code' => $primOkved->getCode()];
        } else {
            $primOkved = null;
        }

        if ($this->getParentCompany() === null) {
            $entityForAuthoritiesAndDocuments = $this;
        } else {
            $entityForAuthoritiesAndDocuments = $this->getParentCompany();
        }

        $taxService = $entityForAuthoritiesAndDocuments->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'FEDERAL_TAX_SERVICE'
        )->map(
            fn(SubjectAuthority $authority) =>
            ['name' => $authority->getName(), 'code' => $authority->getCode()]
        )->toArray();

        $taxReport = $entityForAuthoritiesAndDocuments->documents->filter(
            fn(SubjectDocument $document) =>
                $document->getType() === 'FTS_REPORT'
        )->map(
            fn(SubjectDocument $document) =>
            ['type' => $document->getType(), 'issueDate' => DateHelper::formatDate($document->getIssueDate())]
        )->toArray();

        $pensionFond = $entityForAuthoritiesAndDocuments->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'PENSION_FUND'
        )->map(
            fn(SubjectAuthority $authority) =>
            ['name' => $authority->getName(), 'code' => $authority->getCode()]
        )->toArray();

        $pensionFondRegistration = $entityForAuthoritiesAndDocuments->documents->filter(
            fn(SubjectDocument $document) =>
                $document->getType() === 'PF_REGISTRATION'
        )->map(
            fn(SubjectDocument $document) =>
            ['type' => $document->getType(), 'issueDate' => DateHelper::formatDate($document->getIssueDate())]
        )->toArray();

        $socialInsuranceFond = $entityForAuthoritiesAndDocuments->authorities->filter(
            fn(SubjectAuthority $authority) =>
                $authority->getType() === 'SOCIAL_INSURANCE_FUND'
        )->map(
            fn(SubjectAuthority $authority) =>
            ['name' => $authority->getName(), 'code' => $authority->getCode()]
        )->toArray();

        $socialInsuranceFondRegistration = $entityForAuthoritiesAndDocuments->documents->filter(
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

        $managers = $this->managers->map(
            function (CompanyManager $companyManager) {

                $individual = $companyManager->getIndividualManager();

                if ($individual !== null) {
                    return [
                        'slug' => $individual->getSlug(),
                        'post' => $companyManager->getPost(),
                        'name' => $individual->getSurname() . ' ' . $individual->getName() . ' ' . $individual->getPatronymic(),
                        'inn' => $individual->getInn(),
                        'startDate' => DateHelper::formatDate($companyManager->getStartDate()),
                        'type' => 'individual'
                    ];
                } else {

                    $company = $companyManager->getCompanyManager();

                    //TODO rework
                    return [
                        'slug' => $company->getSlug(),
                        'post' => $companyManager->getPost(),
                        'name' => $company->getName(),
                        'inn' => $company->getInn(),
                        'startDate' => DateHelper::formatDate($companyManager->getStartDate()),
                        'type' => 'company'
                    ];
                }
            }
        )->toArray();

        $successors = $this->history->filter(function (CompanyHistory $companyHistory) {

            if ($companyHistory->getSuccessorCompany() !== null) {
                return true;
            }

            return false;
        });

        $successors = $successors->map(fn(CompanyHistory $companyHistory) => [
            'name' => $companyHistory->getSuccessorCompany()->getName(),
            'inn' => $companyHistory->getSuccessorCompany()->getInn(),
            'slug' => $companyHistory->getSuccessorCompany()->getSlug(),
        ])->toArray();

        $predecessors = $this->history->filter(function (CompanyHistory $companyHistory) {

            if ($companyHistory->getPredecessorCompany() !== null) {
                return true;
            }

            return false;
        });

        $predecessors = $predecessors->map(fn(CompanyHistory $companyHistory) => [
            'name' => $companyHistory->getPredecessorCompany()->getName(),
            'inn' => $companyHistory->getPredecessorCompany()->getInn(),
            'slug' => $companyHistory->getPredecessorCompany()->getSlug(),
        ])->toArray();

        $filials = $this->children->map(fn(Company $company) => [
            'name' => $company->getName(),
            'slug' => $company->getSlug(),
            'opf' => $company->getOpf(),
            'inn' => $company->getInn(),
            'kpp' => $company->getKpp(),
        ])->toArray();

        if ($filials === []) {
            $filials = null;
        }

        if ($this->finance !== null) {
            $finances = [
                'income' => $this->finance->getIncome(),
                'debt' => $this->finance->getDebt(),
                'expense' => $this->finance->getExpense(),
                'revenue' => $this->finance->getRevenue(),
                'taxSystem' => $this->finance->getTaxSystem(),
                'year' => $this->finance->getYear(),
                'penalty' => $this->finance->getPenalty(),
            ];
        } else {
            $finances = null;
        }

        if ($this->licenses->count() > 0) {
            $licenses = $this->licenses->map(fn(SubjectLicence $license) => [
                'series' => $license->getSeries(),
                'number' => $license->getNumber(),
                'issueDate' => DateHelper::formatDate($license->getIssueDate()),
                'issueAuthority' => $license->getIssueAuthority(),
                'suspendDate' => DateHelper::formatDate($license->getSuspendDate()),
                'validFrom' => DateHelper::formatDate($license->getValidFrom()),
                'validTo' => DateHelper::formatDate($license->getValidTo()),
                'activities' => $license->getActivities(),
                'addresses' => $license->getAddresses(),
            ])->toArray();
        } else {
            $licenses = null;
        }

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

        $shares = OwnershipHelper::calculateAllFractions($this->foundedBy->toArray());

        if ($this->foundedBy->count() !== 0 && $shares !== null) {
            $founders = $this->foundedBy->map(function (Founder $founder) use ($shares) {

                if ($founder->getFounderCompany() !== null) {
                    return [
                        'id' => $founder->getFounderCompany()->getId(),
                        'name' => $founder->getFounderCompany()->getOpf()->getAbbreviation() . ' ' . $founder->getFounderCompany()->getName(),
                        'share' => $shares[$founder->getId()],
                        'type' => 'company',
                        'slug' => $founder->getFounderCompany()->getSlug(),
                    ];
                } elseif ($founder->getFounderIndividual() !== null) {
                    return [
                        'id' => $founder->getFounderIndividual()->getId(),
                        'name' => $founder->getFounderIndividual()->getSurname()
                            . ' ' . $founder->getFounderIndividual()->getName()
                            . ' ' . $founder->getFounderIndividual()->getPatronymic(),
                        'share' => $shares[$founder->getId()],
                        'type' => 'person',
                        'slug' => $founder->getFounderIndividual()->getSlug(),
                    ];
                }

                return null;

            })->toArray();
        } else {
            $founders = null;
        }

        $data = $this->getData();

        $hasFounders = !empty($data['founders'] ?? []);
        $hasPredecessors = !empty($data['predecessors'] ?? []);
        $hasSuccessors = !empty($data['successors'] ?? []);

        $foundersStatus = match (true) {
            !$hasFounders => 'empty',
            !$this->foundersProcessed => 'processing',
            default => 'ok',
        };

        $historyStatus = match (true) {
            !$hasPredecessors && !$hasSuccessors => 'empty',
            !$this->historyProcessed => 'processing',
            default => 'ok',
        };

        $result = [
            'id' => $this->id,
            'inn' => $this->inn,
            'ogrn' => $this->ogrn,
            'kpp' => $this->kpp,
            'opf' => $this->opf,
            'type' => self::COMPANY_TYPE,
            'slug' => $this->slug,
            'name' => $this->name,
            'address' => $this->address?->getFullAddress(),
            'okved' => $primOkved,
            'taxService' => $taxService,
            'taxReport' => $taxReport,
            'pensionFond' => $pensionFond,
            'pensionFondRegistration' => $pensionFondRegistration,
            'managers' => $managers,
            'filials' => $filials,
            'finances' => $finances,
            'licenses' => $licenses,
            'socialInsuranceFond' => $socialInsuranceFond,
            'socialInsuranceFondRegistration' => $socialInsuranceFondRegistration,
            'secondaryOkveds' => $secondaryOkveds,
            'status' => $status,
            'phones' => $this->phones,
            'emails' => $this->emails,
            'founders' => $founders,
            'foundersProcessed' => $this->foundersProcessed,
            'historyProcessed' => $this->historyProcessed,
            'foundersStatus' => $foundersStatus,
            'historyStatus' => $historyStatus,
            'okpo' => $this->okpo,
            'okato' => $this->okato,
            'oktmo' => $this->oktmo,
            'okogu' => $this->okogu,
            'okfs' => $this->okfs,
            'successors' => $successors,
            'predecessors' => array_values($predecessors),
            'authorities' => $this->authorities->toArray(),
            'documents' => $this->documents->toArray(),
            'capital' => $this->capital?->getValue(), // можно дополнительно уточнить, какие данные о капитале возвращать
            'state' => $this->state, // аналогично можно детализировать информацию о состоянии
        ];

        // Не включаем родительскую компанию и дочерние компании в сериализацию
        if ($this->getParentCompany()) {
            $result['parent_company'] = $this->getParentCompany()->getId(); // возвращаем только ID
        }

        // Если дочерние компании нужно сериализовать, их можно обработать отдельно
        $children = $this->getChildren()->map(fn($child) => $child->getId()); // Получаем только ID дочерних компаний
        $result['children'] = $children->toArray();

        return $result;
    }

    public function addOkved(SubjectOkved $subjectOkved): self
    {
        if (!$this->companyOkveds->contains($subjectOkved)) {
            $this->companyOkveds->add($subjectOkved);
            $subjectOkved->setCompany($this);
        }

        return $this;
    }

    public function getCompanyOkveds(): Collection
    {
        return $this->companyOkveds;
    }

    public function addFounder(Founder $founder): self
    {
        if (!$this->founders->contains($founder)) {
            $this->founders->add($founder);
            $founder->setFoundedCompany($this);
        }

        return $this;
    }

    public function addManager(CompanyManager $manager): self
    {
        if (!$this->managers->contains($manager)) {
            $this->managers->add($manager);
            $manager->setManagedCompany($this);
        }

        return $this;
    }

    public function addLicense(SubjectLicence $license): self
    {
        if (!$this->licenses->contains($license)) {
            $this->licenses->add($license);
            $license->setCompany($this);
        }

        return $this;
    }

    public function getLicenses(): ?Collection
    {
        return $this->licenses;
    }

    public function addDocument(SubjectDocument $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setCompany($this);
        }

        return $this;
    }

    public function addAuthority(SubjectAuthority $authority): self
    {
        if (!$this->authorities->contains($authority)) {
            $this->authorities->add($authority);
            $authority->setCompany($this);
        }

        return $this;
    }

    public function addManagedCompany(CompanyManagementCo $managedCompany): self
    {
        if (!$this->managedCompanies->contains($managedCompany)) {
            $this->managedCompanies->add($managedCompany);
            $managedCompany->setManagerCompany($this);
        }

        return $this;
    }

    public function addManagerCompany(CompanyManagementCo $managerCompany): self
    {
        if (!$this->managerCompanies->contains($managerCompany)) {
            $this->managerCompanies->add($managerCompany);
            $managerCompany->setManagedCompany($this);
        }

        return $this;
    }

    public function isFoundersProcessed(): bool
    {
        return $this->foundersProcessed;
    }

    public function setFoundersProcessed(bool $foundersProcessed): void
    {
        $this->foundersProcessed = $foundersProcessed;
    }

    public function isAffiliatedProcessed(): bool
    {
        return $this->affiliatedProcessed;
    }

    public function setAffiliatedProcessed(bool $affiliatedProcessed): void
    {
        $this->affiliatedProcessed = $affiliatedProcessed;
    }

    public function isHistoryProcessed(): bool
    {
        return $this->historyProcessed;
    }

    public function setHistoryProcessed(true $historyProcessed): self
    {
        $this->historyProcessed = $historyProcessed;

        return $this;
    }

    public function isManagersProcessed(): bool
    {
        return $this->managersProcessed;
    }

    public function setManagersProcessed(true $managersProcessed): self
    {
        $this->managersProcessed = $managersProcessed;

        return $this;
    }
}
