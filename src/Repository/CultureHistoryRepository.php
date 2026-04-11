<?php

namespace App\Repository;

use App\Entity\Culture;
use App\Entity\CultureHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CultureHistory>
 */
class CultureHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CultureHistory::class);
    }

    /**
     * @return CultureHistory[]
     */
    public function findByCultureOrderedDesc(Culture $culture): array
    {
        return $this->createQueryBuilder('history')
            ->leftJoin('history.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->andWhere('history.culture = :culture')
            ->setParameter('culture', $culture)
            ->orderBy('history.performedAt', 'DESC')
            ->addOrderBy('history.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
