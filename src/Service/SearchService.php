<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\Individual;
use App\Mapper\CompanyDataMapper;
use App\Mapper\IndividualDataMapper;
use App\Model\CompanyListingModel;
use App\Model\IndividualListingModel;
use App\Repository\CompanyRepository;
use App\Repository\IndividualRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class SearchService
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private IndividualRepository $individualRepository,
        private CompanyService $companyService,
        private IndividualService $individualService,
        private DadataService $dadataService,
        private EntityManagerInterface $em,
        private SphinxSearchService $sphinxSearchService,
    ) {}

    /**
     * @return array{items: array, total: int}
     */
    public function search(
        string $identifier,
        array  $filters,
        int    $page = 1,
        int    $perPage = 10,
    ): array {
        // 1) определяем типы, которые ищем
        $types = isset($filters['subjectType'])
            ? array_map('trim', explode(',', $filters['subjectType']))
            : ['Company', 'Individual'];

        // 2) если чисто цифры — однозначно сузим тип по длине
        $isStrictId = ctype_digit($identifier) && in_array(mb_strlen($identifier), [9,10,13, 12], true);

        if ($filters['search-page'] == 'false') {
            $filters['search-page'] = false;
        }

        if ($isStrictId) {
            if (!$filters['search-page']) {
                $company = $this->ensureCompanyExists($identifier, mb_strlen($identifier));

                if ($company) {
                    return [
                        'items' => [ new CompanyListingModel($company) ],
                        'total' => 1,
                    ];
                }
                $individual = $this->ensureIndividualExists($identifier, mb_strlen($identifier));
                return [
                    'items' => $individual ? [ new IndividualListingModel($individual) ] : [],
                    'total' => $individual ? 1 : 0,
                ];
            }
            // 1) создаём в БД, если нужно
            if (in_array('Company', $types, true)) {
                $company = $this->ensureCompanyExists($identifier, mb_strlen($identifier));

                return [
                    'items' => $company ? [ new CompanyListingModel($company) ] : [],
                    'total' => $company ? 1 : 0,
                ];
            }
            if (in_array('Individual', $types, true)) {
                $individual = $this->ensureIndividualExists($identifier, mb_strlen($identifier));
                return [
                    'items' => $individual ? [ $individual ] : [],
                    'total' => $individual ? 1 : 0,
                ];
            }
        }

        $items = [];
        $total = 0;

        $companyItems = $this->sphinxSearchService->fetchFiltered($identifier, $filters, $page, $perPage);
        $companyTotal = $this->sphinxSearchService->countFiltered($identifier, $filters);

        $items = array_merge($items, $companyItems);
        $total += $companyTotal;

        return ['items' => $items, 'total' => $total];
    }

    /**
     * Проверяем, есть ли в БД Company по нужному полю (kpp|inn|ogrn),
     * если нет — единоразово подгружаем из Dadata.
     */
    private function ensureCompanyExists(string $ident, int $len): ?Company
    {
        // 1) проверяем только по тому полю, которое соответствует длине
        $field = match($len) {
            9  => 'kpp',
            10 => 'inn',
            13 => 'ogrn',
            default => null,
        };
        if (!$field) {
            return null;
        }

        $company = $this->companyRepository->findOneBy([$field => $ident]);
        if ($company !== null) {
            return $company;
        }

        $resp = $this->dadataService->findSubjectByIdAndKwards($ident, [
            'branch_type' => 'MAIN',
        ]);

        $data = $resp[0]['data'] ?? null;
        if (!$data) {
            return null;
        }

        $model = CompanyDataMapper::map($data);
        $company = $this->companyService->createCompany($model);

        $this->em->flush();

        return $company;
    }

    /**
     * Аналогично для Individual (ИНН 12 цифр).
     */
    private function ensureIndividualExists(string $ident, int $len): ?Individual
    {
        $field = match($len) {
            15  => 'ogrnip',
            12 => 'inn',
            default => null,
        };
        if (!$field) {
            return null;
        }
        $individual = $this->individualRepository->findOneBy(['inn' => $ident]);

        if ($individual !== null) {
            return $individual;
        }

        $resp = $this->dadataService->findSubjectByIdAndKwards($ident, [
            'branch_type' => 'MAIN',
        ]);

        $data = $resp[0]['data'] ?? null;
        if (!$data) {
            return null;
        }

        $model = IndividualDataMapper::map($data);
        $individual = $this->individualService->createIndividual($model);

        $this->em->flush();

        return $individual;
    }
}