<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Individual;
use App\Entity\SubjectLicence;
use App\Model\CompanyDataModel;
use App\Model\IndividualDataModel;

class LicenseService
{
    public function createLicenses(
        Individual|Company $subject,
        CompanyDataModel|IndividualDataModel $subjectDataModel
    ): void {

        if (null === $subjectDataModel->getLicenses()) {
            return;
        }

        foreach ($subjectDataModel->getLicenses() as $license) {

            $licenseEntity = new SubjectLicence();
            if ($subject instanceof Company) {
                $licenseEntity->setCompany($subject);
            }

            if (!isset($license['issue_date'])) {
                $issueDate = null;
            } else {
                $issueDate = \DateTimeImmutable::createFromFormat('U', ($license['issue_date']) / 1000);
            }

            if (!isset($license['suspend_date'])) {
                $suspendDate = null;
            } else {
                $suspendDate = \DateTimeImmutable::createFromFormat('U', ($license['suspend_date']) / 1000);
            }

            if (!isset($license['valid_from'])) {
                $validFrom = null;
            } else {
                $validFrom = \DateTimeImmutable::createFromFormat('U', ($license['valid_from']) / 1000);
            }

            if (!isset($license['valid_to'])) {
                $validTo = null;
            } else {
                $validTo = \DateTimeImmutable::createFromFormat('U', ($license['valid_to']) / 1000);
            }

            $licenseEntity->setNumber($license['number'])->setSeries($license['series'])
                ->setIssueDate($issueDate)
                ->setIssueAuthority($license['issue_authority'])
                ->setSuspendDate($suspendDate)
                ->setSuspendAuthority($license['suspend_authority'])
                ->setValidFrom($validFrom)
                ->setValidTo($validTo)
                ->setActivities($license['activities'])
                ->setAddresses($license['addresses']);

            $subject->addLicense($licenseEntity);
        }
    }
}