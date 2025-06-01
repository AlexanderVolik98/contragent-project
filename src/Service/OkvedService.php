<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Individual;
use App\Entity\Okved;
use App\Entity\SubjectOkved;
use App\Model\IndividualDataModel;
use App\Repository\OkvedRepository;
use App\Model\CompanyDataModel;
use Doctrine\ORM\EntityManagerInterface;

readonly final class OkvedService
{
    public function __construct(
        private OkvedRepository $okvedRepository,
        private EntityManagerInterface $em
    ) {}

    public function createOkvedsRelations(
        Company|Individual $subject,
        CompanyDataModel|IndividualDataModel $subjectDataModel,
    ): void {

        $primaryCode = $subjectDataModel->getOkved();
        $primaryRaw = null;

        if ($subjectDataModel->getOkveds() === null && $subjectDataModel->getOkved() === null) {
            return;
        }

        // Ищем основной оквед в массиве всех окведов
        foreach ($subjectDataModel->getOkveds() as $okved) {
            if ($okved['main'] === true && $okved['code'] === $primaryCode) {
                $primaryRaw = $okved;
                break;
            }
        }

        if ($primaryRaw !== null) {
            $primaryOkved = $this->okvedRepository->find($primaryRaw['code']);
            if (!$primaryOkved && $primaryRaw['name']) {

                $primaryOkved = (new Okved())
                    ->setCode($primaryRaw['code'])
                    ->setName($primaryRaw['name'])
                    ->setParentCode(null)
                    ->setComment('');

                $this->em->persist($primaryOkved);
            }

            if ($primaryOkved) {

                $primarySubjectOkved = (new SubjectOkved())
                    ->setOkved($primaryOkved)
                    ->setPrimary(true);

                if ($subject instanceof Individual) {
                    $primarySubjectOkved->setIndividual($subject);
                }

                if ($subject instanceof Company) {
                    $primarySubjectOkved->setCompany($subject);
                }

                $subject->addOkved($primarySubjectOkved);
            }
        }

        foreach ($subjectDataModel->getOkveds() as $okved) {
            if ($okved['main'] === true) {
                continue; // уже обработали основной
            }

            $secondaryOkved = $this->okvedRepository->find($okved['code']);
            if (!$secondaryOkved) {
                $secondaryOkved = (new Okved())
                    ->setCode($okved['code'])
                    ->setName($okved['name'])
                    ->setSection(substr($okved['code'], 0, 1))
                    ->setParentCode(null)
                    ->setComment('');

                $this->em->persist($secondaryOkved);
            }

            $subjectOkved = (new SubjectOkved())
                ->setOkved($secondaryOkved)
                ->setPrimary(false);

            if ($subject instanceof Individual) {
                $subjectOkved->setIndividual($subject);
            }

            if ($subject instanceof Company) {
                $subjectOkved->setCompany($subject);
            }

            $subject->addOkved($subjectOkved);
        }

    }

    public function createOkvedForFilial(Company $filialCompany, Company $mainCompany): void
    {
        foreach ($mainCompany->getCompanyOkveds() as $companyOkved) {
            $filialOkved = clone $companyOkved;
            $filialOkved->setId(null)->setCompany($filialCompany);
            $filialCompany->addOkved($filialOkved);
        }
    }
}
