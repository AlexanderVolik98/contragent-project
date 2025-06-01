<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Individual;
use App\Entity\CompanyManager;
use App\Entity\SubjectAddress;
use App\Entity\SubjectAuthority;
use App\Entity\SubjectDocument;
use App\Entity\SubjectState;
use App\Enum\SubjectStatusEnum;
use App\Enum\ManagerTypeEnum;
use App\Model\CompanyDataModel;
use App\Model\IndividualDataModel;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

readonly final class AuthorityService
{
    public function __construct()
    {
    }

    public function createAuthorities(
        Company|Individual $subject,
        CompanyDataModel|IndividualDataModel $subjectDataModel
    ): void {

        if (empty($subjectDataModel->getAuthorities())) {
            return;
        }

        foreach ($subjectDataModel->getAuthorities() as $authority) {

            if (null === $authority) {
                continue;
            }

            $subjectAuthority = (new SubjectAuthority())->setType($authority['type'])
                ->setName($authority['name'])
                ->setCode($authority['code'])
                ->setAddress($authority['address']);

            $subject->addAuthority($subjectAuthority);
        }
    }
}