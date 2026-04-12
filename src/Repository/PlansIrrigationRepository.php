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
     * Récupère tous les plans d'irrigation d'un agriculteur (via ses cultures)
     */
    public function findByAgriculteur(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.culture', 'c')
            ->where('c.proprietaire_id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère un plan avec ses jours d'irrigation (relation OneToMany)
     */
    public function findByProprietaire(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->join('App\Entity\Culture', 'c', 'WITH', 'c.id = p.id_culture')
            ->where('c.proprietaire_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('p.date_demande', 'DESC')
            ->getQuery()
            ->getResult();
    }
}