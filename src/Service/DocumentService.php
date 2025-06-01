<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Individual;
use App\Entity\CompanyManager;
use App\Entity\SubjectAddress;
use App\Entity\SubjectDocument;
use App\Entity\SubjectState;
use App\Enum\SubjectStatusEnum;
use App\Enum\ManagerTypeEnum;
use App\Model\CompanyDataModel;
use App\Model\IndividualDataModel;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

readonly final class DocumentService
{
    public function __construct() {}

    public function createDocuments(
        Company|Individual $subject,
        CompanyDataModel|IndividualDataModel $subjectDataModel
    ): void {
        if (empty($subjectDataModel->getDocuments())) {
            return;
        }

        foreach ($subjectDataModel->getDocuments() as $docType => $document) {

            if (empty($document)) {
                $subjectDocument = (new SubjectDocument($docType))
                    ->setSeries(null)
                    ->setNumber(null)
                    ->setIssueDate(null)
                    ->setIssueAuthority(null);
            } else {
                $issueDate = DateTimeImmutable::createFromFormat(
                    'U', ($document["issue_date"] ?? 0) / 1000
                );

                $subjectDocument = (new SubjectDocument($document["type"]))
                    ->setSeries($document["series"])
                    ->setNumber($document["number"])
                    ->setIssueDate($issueDate)
                    ->setIssueAuthority($document["issue_authority"]);
            }

            $subject->addDocument($subjectDocument);
        }
    }
}