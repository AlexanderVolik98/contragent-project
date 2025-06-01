<?php

namespace App\Mapper;

use App\Model\IndividualDataModel;

class IndividualDataMapper
{
    public static function map(array $data): IndividualDataModel
    {
        return new IndividualDataModel(
            id: null,
            inn: $data['inn'] ?? null,
            name: $data['fio']['name'] ?? null,
            surname: $data['fio']['surname'] ?? null,
            patronymic: $data['fio']['patronymic'] ?? null,
            gender: $data['fio']['gender'] ?? null,
            okpo: $data['okpo'] ?? null,
            okato: $data['okato'] ?? null,
            ogrnip: $data['ogrn'] ?? null,
            oktmo: $data['oktmo'] ?? null,
            okogu: $data['okogu'] ?? null,
            okfs: $data['okfs'] ?? null,
            state: $data['state'] ?? null,
            address: $data['address'] ?? null,
            okved: $data['okved'] ?? null,
            finance: $data['finance'] ?? null,
            documents: $data['documents'] ?? null,
            okveds: $data['okveds'] ?? null,
            authorities: $data['authorities'] ?? null,
            phones: $data['phones'] ?? null,
            emails: $data['emails'] ?? null,
            rawData: $data,
            hid: $data['hid'] ?? null,
        );
    }
}