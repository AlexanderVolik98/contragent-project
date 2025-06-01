<?php

namespace App\Model;

class CompanyListModel
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $inn,
        public ?string $ogrn,
        public ?string $kpp,
        public string $status,
        public ?string $okved,
        public ?string $opfAbbreviation,
    ) {}
}