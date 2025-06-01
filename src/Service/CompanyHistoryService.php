<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyHistory;
use App\Mapper\CompanyDataMapper;
use App\Model\CompanyDataModel;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;

class CompanyHistoryService
{
    private ?CompanyService $companyService = null;

    public function __construct(
        private CompanyRepository      $companyRepository,
        private DadataService          $dadataService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function setCompanyService(CompanyService $companyService): void
    {
        $this->companyService = $companyService;
    }

    public function createCompanyHistory(Company $company, CompanyDataModel $companyDataModel): void
    {
        if (null !== $companyDataModel->getSuccessors()) {

            foreach ($companyDataModel->getSuccessors() as $successor) {

                $successorCompany = null;

                if (isset($successor['inn'])) {
                    $successorCompany = $this->getCompany($successor['inn']); //inn может быть null
                } elseif (isset($successor['ogrn'])) {
                    $successorCompany = $this->getCompany($successor['ogrn']);
                }

                if (null !== $successorCompany) {
                    $this->entityManager->persist(
                        (new CompanyHistory())
                            ->setCompany($company)
                            ->setSuccessorCompany($successorCompany)
                    );
                } else {

                    $companySmallData = [
                        'name' => $successor['name'],
                        'inn' => $successor['inn'] ?? null,
                        'ogrn' => $successor['ogrn'] ?? null,
                    ];

                    $companyDataModel = CompanyDataMapper::map($companySmallData);

                    $successorCompany = $this->companyService->createCompany($companyDataModel);

                    $this->entityManager->persist(
                        (new CompanyHistory())
                            ->setCompany($company)
                            ->setSuccessorCompany($successorCompany)
                    );
                }
            }
        }

        if (null !== $companyDataModel->getPredecessors()) {

            foreach ($companyDataModel->getPredecessors() as $predecessor) {

                $predecessorCompany = null;

                if (isset($predecessor['inn'])) {
                    $predecessorCompany = $this->getCompany($predecessor['inn']); //inn может быть null
                } elseif (isset($predecessor['ogrn'])) {
                    $predecessorCompany = $this->getCompany($predecessor['ogrn']);
                }

                if (null !== $predecessorCompany) {
                    $this->entityManager->persist(
                        (new CompanyHistory())
                            ->setCompany($company)
                            ->setPredecessorCompany($predecessorCompany)
                    );
                } else {

                    $companySmallData = [
                        'name' => $predecessor['name'],
                        'inn' => $predecessor['inn'] ?? null,
                        'ogrn' => $predecessor['ogrn'] ?? null,
                    ];

                    $companyDataModel = CompanyDataMapper::map($companySmallData);

                    $predecessorCompany = $this->companyService->createCompany($companyDataModel);

                    $this->entityManager->persist(
                        (new CompanyHistory())
                            ->setCompany($company)
                            ->setPredecessorCompany($predecessorCompany)
                    );
                }
            }
        }
    }

    public function getCompany(string $identifier): ?Company
    {
        $company = null;

        if (iconv_strlen($identifier, 'UTF-8') === 10) {
            $company = $this->companyRepository->findOneBy(['inn' => $identifier, 'isMain' => true]);
        } else if (iconv_strlen($identifier, 'UTF-8') === 13) {
            $company = $this->companyRepository->findOneBy(['ogrn' => (int)$identifier, 'isMain' => true]); //надо было кастовать
        }

        if (null === $company) {

            $companyData = $this->dadataService->findSubjectByIdAndKwards($identifier, ['branch_type' => 'MAIN']);

            if (!isset($companyData[0])) {
                return null; //в dadata нет такой компании правопреемника или правопредшественника
            } else {
                $companyDataModel = CompanyDataMapper::map($companyData[0]['data']);
            }

            $company = $this->companyService->createCompany($companyDataModel);
        }

        return $company;
    }
}