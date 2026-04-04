<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Reservation;
use App\Enum\ReservationStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * @return Reservation[]
     */
    public function findLatestPending(int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.statut = :statut')
            ->setParameter('statut', ReservationStatut::EN_ATTENTE)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
