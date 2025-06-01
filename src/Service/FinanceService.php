<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\SubjectFinance;
use App\Entity\Individual;
use App\Model\CompanyDataModel;
use App\Model\IndividualDataModel;

readonly final class FinanceService
{
    public function __construct() {}

    public function createFinance(Company|Individual $subject, CompanyDataModel|IndividualDataModel $subjectDataModel): void
    {
        if (empty($subjectDataModel->getFinance())) {
            return;
        }

        $finances = $subjectDataModel->getFinance();

        $taxSystem = $finances['tax_system'];
        $income = $finances['income'];
        $expense = $finances['expense'];
        $revenue = $finances['revenue'];
        $debt = $finances['debt'];
        $penalty = $finances['penalty'];
        $year = $finances['year'];

        $finance = (new SubjectFinance())->setIncome($income)
            ->setDebt($debt)
            ->setPenalty($penalty)
            ->setYear($year)
            ->setRevenue($revenue)
            ->setTaxSystem($taxSystem)
            ->setExpense($expense);

        if ($subject instanceof Individual) {
            $finance->setIndividual($subject);
        }

        if ($subject instanceof Company) {
            $finance->setCompany($subject);
        }

        $subject->setFinance($finance);
    }
}