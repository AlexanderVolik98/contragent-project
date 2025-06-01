<?php

namespace App\Model;

class IndividualListModel
{
    public function __construct(
        public int $id,
        public string $fullName,
        public ?string $inn,
        public ?string $okved,
    ) {}
}