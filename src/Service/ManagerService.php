<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyManager;
use App\Enum\ManagerTypeEnum;
use App\Mapper\CompanyDataMapper;
use App\Mapper\IndividualDataMapper;
use App\Model\CompanyDataModel;
use App\Repository\CompanyRepository;
use App\Repository\IndividualRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class ManagerService
{
    private ?CompanyService $companyService = null;

    public function __construct(
        private CompanyRepository    $companyRepository,
        private IndividualRepository $individualRepository,
        private IndividualService    $individualService,
        private DadataService        $dadataService,
    ) {}

    public function setCompanyService(CompanyService $companyService): void
    {
        $this->companyService = $companyService;
    }

    public function createManager(Company $company, CompanyDataModel $companyDataModel): void
    {
        if (empty($companyDataModel->getManagers())) {
            return;
        }

        foreach ($companyDataModel->getManagers() as $incomingManager) {

            if (!isset($incomingManager['start_date'])) {
                $startDate = null;
            } else {
                $startDate = \DateTimeImmutable::createFromFormat('U', ($incomingManager['start_date']) / 1000);
            }

            $managerType = ManagerTypeEnum::tryFrom($incomingManager['type']);

            if ($managerType === ManagerTypeEnum::LEGAL) {

                $companyManagerEntity = $this->companyRepository->findOneBy(['inn' => $incomingManager['inn'], 'isMain' => true]);

                if (null === $companyManagerEntity) {
                    $incomingCompany = $this->dadataService->findSubjectByIdAndKwards($incomingManager['inn']);

                    if ([] === $incomingCompany) {
                        $incomingManagerModel = CompanyDataMapper::map($incomingManager);
                        // Используем установленный CompanyService
                        $companyManagerEntity = $this->companyService->createCompany($incomingManagerModel);
                    } else {
                        $incomingCompanyModel = CompanyDataMapper::map($incomingCompany[0]['data']);
                        $companyManagerEntity = $this->companyService->createCompany($incomingCompanyModel);
                    }
                }

                $companyManager = (new CompanyManager())
                    ->setManagedCompany($company)
                    ->setCompanyManager($companyManagerEntity)
                    ->setType($managerType)
                    ->setInvalidity($incomingManager['invalidity'])
                    ->setDadataId($incomingManager['hid'])
                    ->setStartDate($startDate);

            } else {
                if ($incomingManager['inn']) {
                    $managerIndividual = $this->individualRepository->findOneBy(['inn' => $incomingManager['inn']]);

                    if (null === $managerIndividual) {
                        $incomingIndividual = $this->dadataService->findSubjectByIdAndKwards($incomingManager['inn']);

                        if ([] === $incomingIndividual) {
                            $incomingManagerModel = IndividualDataMapper::map($incomingManager);
                            $managerIndividual = $this->individualService->createIndividual($incomingManagerModel);
                        } else {
                            $incomingIndividualModel = IndividualDataMapper::map($incomingIndividual[0]['data']);
                            $managerIndividual = $this->individualService->createIndividual($incomingIndividualModel);
                        }
                    }
                } else {
                    $incomingManagerModel = IndividualDataMapper::map($incomingManager);

                    $slugBase = sprintf(
                        '%s-%s%s-%s',
                        $incomingManagerModel->getSurname(),
                        mb_strtolower(mb_substr($incomingManagerModel->getName(), 0, 1)),
                        mb_strtolower(mb_substr($incomingManagerModel->getPatronymic(), 0, 1)),
                        null,
                    );

                    $slug = (new AsciiSlugger())->slug($slugBase)->lower()->toString();

                    $slug .= $incomingManagerModel->getHid();
                    $managerIndividual = $this->individualRepository->findOneBy(['slug' => $slug]);
                    if (null === $managerIndividual) {
                        $managerIndividual = $this->individualService->createIndividual($incomingManagerModel);
                    }
                }

                $companyManager = (new CompanyManager())
                    ->setManagedCompany($company)
                    ->setIndividualManager($managerIndividual)
                    ->setType($managerType)
                    ->setInvalidity($incomingManager['invalidity'])
                    ->setDadataId($incomingManager['hid'])
                    ->setPost($incomingManager['post'])
                    ->setStartDate($startDate);
            }

            $company->addManager($companyManager);
        }
    }
}
