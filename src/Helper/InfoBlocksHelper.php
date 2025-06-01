<?php

namespace App\Helper;

use App\Entity\Company;

class InfoBlocksHelper
{
    public static function orderInfoBlocks(Company $company): array
    {
        $infoBlocks = [];

        if ($company->getFoundedBy()->count() > 0) {
            $infoBlocks[] = 'founders_block';
        }

        if ($company->getChildren()->count() > 0) {
            $infoBlocks[] = 'filials_block';
        }

        if ($company->getFinance() != null) {
            $infoBlocks[] = 'finances_block';
        }

        return $infoBlocks;
    }
}