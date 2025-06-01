<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Individual;
use App\Entity\SubjectState;
use App\Enum\SubjectStatusEnum;
use App\Model\CompanyDataModel;
use App\Model\IndividualDataModel;

readonly final class StateService
{
    public function __construct() {}

    public function createState(Company|Individual $subject, CompanyDataModel|IndividualDataModel $subjectDataModel): void
    {
        // Создание состояния компании (SubjectState) на основе модели
        $stateData = $subjectDataModel->getState();
        $actualityDate = \DateTimeImmutable::createFromFormat('U', ($stateData['actuality_date'] ?? 0) / 1000);
        $registrationDate = \DateTimeImmutable::createFromFormat('U', ($stateData['registration_date'] ?? 0) / 1000);
        $liquidationDate = isset($stateData['liquidation_date'])
            ? \DateTimeImmutable::createFromFormat('U', $stateData['liquidation_date'] / 1000)
            : null;
        $status = SubjectStatusEnum::tryFrom($stateData['status'] ?? 'UNDEFINED');
        $subjectState = (new SubjectState())
            ->setCode($stateData['code'] ?? '')
            ->setActualityDate($actualityDate)
            ->setRegistrationDate($registrationDate)
            ->setLiquidationDate($liquidationDate)
            ->setStatus($status);

        if ($subject instanceof Individual) {
            $subjectState->setIndividual($subject);
        }

        if ($subject instanceof Company) {
            $subjectState->setCompany($subject);

            if (
                $subjectState->getStatus() === SubjectStatusEnum::LIQUIDATED
                && $subjectDataModel->getSuccessors()
            ) {
                $subjectState->setComment('Прекратило деятельность (Прекращение деятельности юридического лица путем реорганизации в форме преобразования)');
            }

            if ($subjectState->getStatus() === SubjectStatusEnum::REORGANIZING) {
                $subjectState->setComment('Юридическое лицо находится в процессе реорганизации');
            }
        }

        $subject->setState($subjectState);
    }
}