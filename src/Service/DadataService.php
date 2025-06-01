<?php

namespace App\Service;

use Dadata\DadataClient;

class DadataService
{
    const int MAXIMUM_POSSIBLE_COUNT = 10000;

    private DadataClient $dadataClient;

    public function __construct(string $dadataToken)
    {
        $this->dadataClient = new DadataClient($dadataToken, null);
    }

    public function findSubjectsByIdForHomePage(string $identifier): array
    {
        return $this->dadataClient->findById("party", $identifier, self::MAXIMUM_POSSIBLE_COUNT);
    }

    public function getAffiliatedCompanies(string $identifier): array
    {
        return $this->dadataClient->findAffiliated($identifier, self::MAXIMUM_POSSIBLE_COUNT);

    }

    public function findSubjectByIdAndKwards(string $identifier, array $kwards = []): array
    {
        return $this->dadataClient->findById("party", $identifier, 10, $kwards);
    }
}
