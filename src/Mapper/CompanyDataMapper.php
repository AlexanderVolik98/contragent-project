<?php

namespace App\Mapper;

use App\Model\CompanyDataModel;

class CompanyDataMapper
{
    /**
     * Преобразует массив входных данных в объект модели CompanyDataModel.
     */
    public static function map(array $data): CompanyDataModel
    {
        if (is_string($data['name'])) {
            $shortName = $data['name'];
            $companyName = $data['name'];
        } elseif (is_array($data['name'])) {
            $companyName = $data['name']['full'];

            if (isset($data['name']['short'])) {
                $shortName = $data['name']['short'];
            } else {
                if (strlen($data['name']['full']) > 150) {
                    $arrNameForShort = explode(' ', $data['name']['full']);
                    $shortName = implode(' ', array_slice($arrNameForShort, 0, 3));
                } else {
                    $shortName = $data['name']['full'];
                }
            }
        }

        return new CompanyDataModel(
            $companyName ?? null,
            $shortName ?? null,
            $data['branch_type'] ?? null,
            $data['inn'] ?? null,
            $data['hid'] ?? null,
            $data['kpp'] ?? null,
            $data['ogrn'] ?? null,
            $data['state'] ?? null,
            $data['address'] ?? [],
            $data['founders'] ?? null,
            $data['managers'] ?? null,
            $data['okved'] ?? null,
            $data['okveds'] ?? [],
            $data['finance'] ?? null,
            $data['capital'] ?? null,
            $data['opf']['short'] ?? null,
            $data['documents'] ?? null,
            $data['authorities'] ?? null,
            $data['okpo'] ?? null,
            $data['okato'] ?? null,
            $data['oktmo'] ?? null,
            $data['okogu'] ?? null,
            $data['okfs'] ?? null,
            $data['successors'] ?? null,
            $data['predecessors'] ?? null,
            $data['phones'] ?? null,
            $data['emails'] ?? null,
            $data['licenses'] ?? null,
            $data,
        );
    }
}
