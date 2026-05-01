<?php

namespace App\Repository;

use App\Entity\PlansIrrigation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlansIrrigation>
 */
class PlansIrrigationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlansIrrigation::class);
    }

    /**
     * @return PlansIrrigation[]
     */
    public function findByAgriculteur(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.culture', 'c')
            ->where('IDENTITY(c.proprietaire_id) = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return PlansIrrigation[]
     */
    public function findByProprietaire(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->join('App\Entity\Culture', 'c', 'WITH', 'c.id = p.culture_id')
            ->where('IDENTITY(c.proprietaire_id) = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('p.date_demande', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
