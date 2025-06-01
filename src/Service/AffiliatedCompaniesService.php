<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyCapital;
use App\Entity\Founder;
use App\Entity\Individual;
use App\Enum\FounderTypeEnum;
use App\Mapper\CompanyDataMapper;
use App\Model\CompanyDataModel;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Throwable;

readonly final class AffiliatedCompaniesService
{
    public function __construct(
        private DadataService          $dadataService,
        private CompanyService         $companyService,
        private CompanyDataMapper      $companyDataMapper,
        private EntityManagerInterface $entityManager,
        private CompanyRepository      $companyRepository,
        private FounderService         $founderService,
    ) {}

    public function createAffiliated(ArrayCollection $subjects): void
    {
        $foundedCompaniesWithFoundedCompanies = new ArrayCollection();

        /** @var Company|Individual $subject */
        foreach ($subjects as $subject) {
            if (null === $subject->getInn()) {
                return;
            }

            try {

                $affiliatedCompanies = $this->dadataService->getAffiliatedCompanies($subject->getInn());

            } catch (Throwable $exception) {
                dd($exception);
            }

            foreach ($affiliatedCompanies as $affiliatedCompany) {

                if ($subject instanceof Company) {

                    $founderCompany = $subject;

                    // так как нет признака для поиска
//                    if (!$affiliatedCompany['data']['inn']) {
//                        dd($founderCompany, $affiliatedCompany);
//                    } ogrn - 1151001013503 ВФК проект
                    // основатель группа компаний сегежа inn - 7703803710

                    if ($affiliatedCompany['data']['inn']) {
                        $affiliatedCompanyExtraData = $this->dadataService->findSubjectsByIdForHomePage($affiliatedCompany['data']['inn']);
                    } elseif ($affiliatedCompany['data']['ogrn']) {
                        $affiliatedCompanyExtraData = $this->dadataService->findSubjectsByIdForHomePage($affiliatedCompany['data']['ogrn']);
                    }

                    $affiliatedCompanyDataModel = $this->companyDataMapper->map($affiliatedCompanyExtraData[0]['data']);

                    $foundedCompany = $this->companyRepository->findOneBy(
                        [
                        'kpp' => $affiliatedCompanyDataModel->getKpp(),
                        'inn' => $affiliatedCompanyDataModel->getInn()
                        ]
                    );

                    if ($foundedCompany !== null) {
                        //создаем только одного основателя (по которому проходимся)
                        $this->founderService->createFounders($affiliatedCompanyDataModel, $foundedCompany, $founderCompany);
                    } else {
                        $foundedCompany = $this->companyService->createCompany($affiliatedCompanyDataModel, $founderCompany);

                        if ($foundedCompany->getInn() !== null) {
                            $foundedCompaniesAtFoundedCompany = $this->dadataService->getAffiliatedCompanies(
                                $foundedCompany->getInn()
                            );
                        } elseif ($foundedCompany->getOgrn() !== null) {
                            $foundedCompaniesAtFoundedCompany = $this->dadataService->getAffiliatedCompanies(
                                $foundedCompany->getOgrn()
                            );
                        }
                    }

                    if (!empty($foundedCompaniesAtFoundedCompany)) {
                        if (!$foundedCompaniesWithFoundedCompanies->contains($foundedCompany)) {
                            $foundedCompaniesWithFoundedCompanies->add($foundedCompany);
                        }
                    }
                }
            }
        }

        // After processing all the founders, start recursion
        if ($foundedCompaniesWithFoundedCompanies->count() > 0) {
            $this->createAffiliated($foundedCompaniesWithFoundedCompanies);
        }

        $this->entityManager->flush();
    }
}