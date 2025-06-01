<?php

namespace App\Repository;

use App\Entity\Opf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OpfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Opf::class);
    }

    public function findOneByAbbreviationInsensitive(string $abbreviation): ?Opf
    {
        return $this->createQueryBuilder('o')
            ->where('LOWER(o.abbreviation) = :abbr')
            ->setParameter('abbr', mb_strtolower($abbreviation))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
