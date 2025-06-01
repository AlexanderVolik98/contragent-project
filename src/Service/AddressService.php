<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Individual;
use App\Entity\CompanyManager;
use App\Entity\Region;
use App\Entity\SubjectAddress;
use App\Entity\SubjectState;
use App\Enum\SubjectStatusEnum;
use App\Model\CompanyDataModel;
use App\Model\IndividualDataModel;
use Doctrine\ORM\EntityManagerInterface;

readonly final class AddressService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function createAddress(
        Company|Individual $subject,
        CompanyDataModel|IndividualDataModel $companyDataModel,
    ): void {
        $addressData = $companyDataModel->getAddress();

        $regionName = $addressData['data']['region'] ?? null;

        $region = null;

        if ($regionName) {
            $region = $this->entityManager->getRepository(Region::class)
                ->createQueryBuilder('r')
                ->where('LOWER(r.name) LIKE LOWER(:region)')
                ->setParameter('region', $regionName . '%')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        $subjectAddress = (new SubjectAddress())
            ->setFullAddress($addressData['value'] ?? '')
            ->setCountry($addressData['data']['country'] ?? '')
            ->setStreet($addressData['data']['street_with_type'] ?? '')
            ->setRegion($region)
            ->setCity($addressData['data']['city'] ?? '');
        $subject->setAddress($subjectAddress);
    }
}