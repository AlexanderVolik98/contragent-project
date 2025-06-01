<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyCapital;
use App\Entity\Opf;
use App\Model\CompanyDataModel;
use App\Repository\CompanyRepository;
use App\Repository\OpfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class CompanyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OkvedService           $okvedService,
        private StateService           $stateService,
        private AddressService         $addressService,
        private FinanceService         $financeService,
        private DocumentService        $documentService,
        private AuthorityService       $authorityService,
        private CompanyRepository      $companyRepository,
        private LicenseService         $licenseService,
        private OpfRepository          $opfRepository,
    ) {}

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    private array $companyCache = [];

    public function createCompany(CompanyDataModel $companyDataModel, ?Company $founderCompany = null): Company
    {
        $inn = $companyDataModel->getInn();
        $ogrn = $companyDataModel->getOgrn();
        $isMain = ($companyDataModel->getBranchType() === 'MAIN');

        $cacheKey = $inn ? $inn . '-' . ($isMain ? 'main' : 'branch') : $ogrn;

        if (isset($this->companyCache[$cacheKey])) {
            return $this->companyCache[$cacheKey];
        }

        $existing = null;
        if ($inn) {
            $existing = $this->companyRepository->findOneBy([
                'inn'    => $inn,
                'isMain' => $isMain,
            ]);
        } elseif ($ogrn) {
            $existing = $this->companyRepository->findOneBy([
                'ogrn'   => $ogrn,
            ]);
        }

        if ($existing) {
            $this->companyCache[$cacheKey] = $existing;
            return $existing;
        }

        $slugBase = (new AsciiSlugger())
            ->slug($companyDataModel->getShortName())
            ->lower()
            ->toString();

        $slug = $slugBase . '-' . ($inn ?? $ogrn);

        if (strlen($slug) > 200) {
            // Делаем обрезку, оставляем место для -inn или -ogrn
            $slugBase = substr($slug, 0, 200 - 15); // 15 — это длина inn/ogrn

            // Собираем полный slug с inn/ogrn
            $slug = $slugBase . '-' . ($inn ?? $ogrn);
        }

        $opfAbbr = $companyDataModel->getOpf();

        $opf = null;
        if ($opfAbbr !== null) {
            $opf = $this->opfRepository->findOneByAbbreviationInsensitive($opfAbbr);

            if (!$opf) {
                // Проверка в UnitOfWork
                $opf = $this->findMatchingOpfInUow($opfAbbr);

                if (!$opf) {
                    $opf = (new Opf())
                        ->setAbbreviation($opfAbbr)
                        ->setFullName('');

                    $this->entityManager->persist($opf);
                }
            }
        }

        $company = new Company();
        $company->setData($companyDataModel->getRawData())
            ->setName(
                mb_convert_case(
                    mb_strtolower($companyDataModel->getNameFull(), 'UTF-8'),
                    MB_CASE_TITLE,
                    'UTF-8'
                )
            )
            ->setOpf($opf)
            ->setOkfs($companyDataModel->getOkfs())
            ->setOkpo($companyDataModel->getOkpo())
            ->setPhones($companyDataModel->getPhones())
            ->setEmails($companyDataModel->getEmails())
            ->setOkato($companyDataModel->getOkato())
            ->setOktmo($companyDataModel->getOktmo())
            ->setOkogu($companyDataModel->getOkogu())
            ->setIsMain($isMain)
            ->setInn($inn)
            ->setKpp($companyDataModel->getKpp())
            ->setDadataId($companyDataModel->getHid())
            ->setOgrn($ogrn)
            ->setSlug($slug);

        if (!$isMain) {
            $parent = $this->companyRepository->findOneBy([
                'inn'    => $inn,
                'isMain' => true,
            ]);
            $company->setParentCompany($parent);
        }

        $this->entityManager->persist($company);
        $this->companyCache[$cacheKey] = $company;

        $this->addressService->createAddress($company, $companyDataModel);
        $this->okvedService->createOkvedsRelations($company, $companyDataModel);
        $this->financeService->createFinance($company, $companyDataModel);
        $this->documentService->createDocuments($company, $companyDataModel);
        $this->authorityService->createAuthorities($company, $companyDataModel);
        $this->stateService->createState($company, $companyDataModel);

        if (!empty($companyDataModel->getCapital()) && $isMain) {
            $capital = (new CompanyCapital())
                ->setCompany($company)
                ->setType($companyDataModel->getCapital()['type'] ?? null)
                ->setValue($companyDataModel->getCapital()['value'] ?? null);
            $this->entityManager->persist($capital);
        }

        $this->licenseService->createLicenses($company, $companyDataModel);

        return $company;
    }

    private function findMatchingOpfInUow(string $abbr): ?Opf
    {
        $uow = $this->entityManager->getUnitOfWork();

        foreach ($uow->getIdentityMap()[Opf::class] ?? [] as $managedOpf) {
            if (mb_strtolower($managedOpf->getAbbreviation()) == mb_strtolower($abbr)) {
                return $managedOpf;
            }
        }

        foreach ($uow->getScheduledEntityInsertions() as $scheduled) {
            if ($scheduled instanceof Opf && mb_strtolower($scheduled->getAbbreviation()) == mb_strtolower($abbr)) {
                return $scheduled;
            }
        }

        return null;
    }

    public function resetCache(): void
    {
        $this->companyCache = [];
    }
}