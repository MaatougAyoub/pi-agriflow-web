<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Reservation;
use App\Enum\ReservationStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
    public function findAllForAdmin(): array
    {
        return $this->createAllForAdminQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    public function createAllForAdminQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            // admin: n5alli ken reservations eli mazel 3andhom annonce valide bech el page admin ma tti7ch
            ->innerJoin('r.annonce', 'a')
            ->addSelect('a')
            ->orderBy('r.createdAt', 'DESC');
    }

    /**
     * @return Reservation[]
     */
    public function findByClientIdForMarketplace(int $clientId): array
    {
        return $this->createByClientIdForMarketplaceQueryBuilder($clientId)
            ->getQuery()
            ->getResult();
    }

    public function createByClientIdForMarketplaceQueryBuilder(int $clientId): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.annonce', 'a')
            ->addSelect('a')
            ->andWhere('r.clientId = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('r.createdAt', 'DESC');
    }

    /**
     * @return Reservation[]
     */
    public function findLatestPending(int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.annonce', 'a')
            ->addSelect('a')
            ->andWhere('r.statut = :statut')
            ->setParameter('statut', ReservationStatut::EN_ATTENTE)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reservation[]
     */
    public function findReceivedByOwnerId(int $ownerId, ?int $annonceId = null): array
    {
        return $this->createReceivedByOwnerIdQueryBuilder($ownerId, $annonceId)
            ->getQuery()
            ->getResult();
    }

    public function createReceivedByOwnerIdQueryBuilder(int $ownerId, ?int $annonceId = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->innerJoin('r.annonce', 'a')
            ->addSelect('a')
            ->andWhere('r.proprietaireId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('r.createdAt', 'DESC');

        if (null !== $annonceId) {
            $queryBuilder
                ->andWhere('a.id = :annonceId')
                ->setParameter('annonceId', $annonceId);
        }

        return $queryBuilder;
    }
}
