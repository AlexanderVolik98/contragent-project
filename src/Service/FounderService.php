<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyManagementCo;
use App\Entity\Founder;
use App\Enum\FounderTypeEnum;
use App\Mapper\CompanyDataMapper;
use App\Mapper\IndividualDataMapper;
use App\Model\CompanyDataModel;
use App\Repository\CompanyRepository;
use App\Repository\FounderRepository;
use App\Repository\IndividualRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

readonly class FounderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FounderRepository      $founderRepository,
        private CompanyRepository      $companyRepository,
        private DadataService          $dadataService,
        private CompanyDataMapper      $companyDataMapper,
        private CompanyService         $companyService,
        private IndividualService      $individualService,
        private IndividualRepository   $individualRepository,
    ) {}

    /**
     * Основной метод создания связей основателей для компании.
     */
    public function createFounders(
        CompanyDataModel $foundedCompanyDataModel,
        Company $foundedCompany,
        ?Company $founderCompany = null
    ): void {
        $founders = $foundedCompanyDataModel->getFounders();

        if (null === $founders) {
            return;
        }

        // Если передана привязанная компания (например, для УК), обрабатываем кейс отдельно
        if (null !== $founderCompany) {
            $this->createFounderForExistingCompany($founders, $founderCompany, $foundedCompany);
            return;
        }

        foreach ($founders as $founderData) {
            if ($founderData['type'] === FounderTypeEnum::LEGAL->name) {
                $this->handleLegalFounder($founderData, $foundedCompany);
            }

            if ($founderData['type'] === FounderTypeEnum::PHYSICAL->name) {
                $this->handlePhysicalFounder($founderData, $foundedCompany);
            }
        }
    }

    private function handleLegalFounder(array $founderData, Company $foundedCompany): void
    {
        $founderCompanyEntity = $this->getOrCreateFounderCompany($founderData);

        if (!$founderCompanyEntity) {
            return;
        }

        if ($this->isFounderLinkExists($founderCompanyEntity, $foundedCompany)) {
            return;
        }

        $founderEntity = $this->buildFounderEntityFromCompany($founderData, $founderCompanyEntity, $foundedCompany);

        $this->entityManager->persist($founderEntity);
    }

    private function handlePhysicalFounder(array $founderData, Company $foundedCompany): void
    {
        $individual = null;
        $dadataResult = null;

        if (!empty($founderData['inn'])) {
            $inn = (string)$founderData['inn'];
            $individual = $this->individualRepository->findOneBy(['inn' => (int)$inn]);

            // если в базе не нашли, пытаемся найти в Dadata
            if (!$individual) {
                $dadataResult = $this->dadataService->findSubjectByIdAndKwards($inn);

                if ($dadataResult) {
                    $individualModel = IndividualDataMapper::map($dadataResult[0]['data']);;
                    $individual = $this->individualService->createIndividual($individualModel);
                }
            }
        }

        // если не нашли в базе и в Dadata, создаём из исходного founderData
        if (!$individual) {
            $individualModel = IndividualDataMapper::map($founderData);
            $individual = $this->individualService->createIndividual($individualModel);
        }

        $startDate = DateTimeImmutable::createFromFormat(
            'U',
            ((int)($founderData['start_date'] ?? 0)) / 1000
        );

        $founder = new Founder();
        $founder
            ->setFoundedCompany($foundedCompany)
            ->setFounderIndividual($individual)
            ->setDadataId($founderData['hid'] ?? null)
            ->setData($founderData)
            ->setShare($founderData['share'] ?? null)
            ->setShareType($founderData['share']['type'] ?? null)
            ->setInvalidity($founderData['invalidity'] ?? null)
            ->setType(FounderTypeEnum::PHYSICAL)
            ->setStartDate($startDate);

        $this->entityManager->persist($founder);
    }

    /**
     * Ищет компанию-основателя по ИНН или ОГРН, если не найдена – пытается создать её через Dadata.
     */
    private function getOrCreateFounderCompany(array $founderData): ?Company
    {
        $identification = $founderData['inn'] ?? $founderData['ogrn'] ?? null;
        if (null === $identification) {
            // Идентификатор не передан, что может быть ошибкой в данных
            return null;
        }

        $founderCompanyEntity = null;
        if (isset($founderData['inn'])) {
            $founderCompanyEntity = $this->companyRepository->findOneBy(['inn' => (int)$founderData['inn']]);
        } elseif (isset($founderData['ogrn'])) {
            $founderCompanyEntity = $this->companyRepository->findOneBy(['ogrn' => (int)$founderData['ogrn']]);
        }

        if ($founderCompanyEntity) {
            return $founderCompanyEntity;
        }

        // Если компания не найдена, создаем новую на основе данных Dadata
        $incomingCompany = $this->dadataService->findSubjectByIdAndKwards($identification, ['branch_type' => 'MAIN']);

        if (empty($incomingCompany)) {
            // Здесь можно залогировать, что данные не найдены
            return null;
        }

        $incomingCompanyModel = $this->companyDataMapper::map($incomingCompany[0]['data']);

        return $this->companyService->createCompany($incomingCompanyModel);
    }

    /**
     * Проверяет, существует ли уже связь Founder между компанией-основателем и компанией, для которой создаются основатели.
     */
    private function isFounderLinkExists(Company $founderCompanyEntity, Company $foundedCompany): bool
    {
        $existingFounder = $this->founderRepository->findOneBy([
            'company' => $founderCompanyEntity,
            'foundedCompany' => $foundedCompany
        ]);
        return $existingFounder !== null;
    }

    private function buildFounderEntityFromCompany(array $founderData, Company $founderCompanyEntity, Company $foundedCompany): Founder
    {
        $startDate = DateTimeImmutable::createFromFormat(
            'U',
            ((int)($founderData['start_date'] ?? 0)) / 1000
        );

        $founderEntity = new Founder();
        $founderEntity->setFoundedCompany($foundedCompany)
            ->setFounderCompany($founderCompanyEntity)
            ->setDadataId($founderData['hid'])
            ->setShare($founderData['share'] ?? null)
            ->setShareType($founderData['share']['type'] ?? null)
            ->setInvalidity($founderData['invalidity'] ?? null)
            ->setType(FounderTypeEnum::LEGAL)
            ->setStartDate($startDate);

        return $founderEntity;
    }

    /**
     * Обрабатывает кейс, когда передана уже привязанная компания (например, для УК).
     */
    private function createFounderForExistingCompany(array $founders, Company $founderCompany, Company $foundedCompany): void
    {
        // Если массив founders отсутствует – обрабатываем как создание управляющей компании
        if (empty($founders)) {
            $companyManagementCo = (new CompanyManagementCo())
                ->setManagedCompany($foundedCompany)
                ->setManagerCompany($founderCompany)
                ->setName($founderCompany->getName())
                ->setData($founderCompany->getData())
                ->setDadataId($founderCompany->getDadataId());

            $founderCompany->addManagedCompany($companyManagementCo);
            $foundedCompany->addManagerCompany($companyManagementCo);
            return;
        }

        // Если founders присутствует, пытаемся найти соответствующего основателя по ИНН
        $matchedFounder = null;
        foreach ($founders as $key => $founderData) {
            if ($founderCompany->getInn() == $founderData['inn']) {
                $matchedFounder = $founderData;
                // Удаляем найденный элемент из массива
                unset($founders[$key]);
                $founders = array_values($founders);
                break;
            }
        }

        if (null !== $matchedFounder) {
            $startDate = DateTimeImmutable::createFromFormat(
                'U',
                ((int)($matchedFounder['start_data'] ?? 0)) / 1000
            );

            $founder = (new Founder())
                ->setDadataId($founderCompany->getDadataId())
                ->setInvalidity(null)
                ->setFoundedCompany($foundedCompany)
                ->setFounderCompany($founderCompany)
                ->setName($founderCompany->getName())
                ->setData($founderCompany->getData())
                ->setType(FounderTypeEnum::LEGAL)
                ->setStartDate($startDate)
                ->setShare($matchedFounder['share'] ?? null)
                ->setShareType($matchedFounder['share']['type'] ?? null);

            $foundedCompany->addFounder($founder);
        }
    }
}
