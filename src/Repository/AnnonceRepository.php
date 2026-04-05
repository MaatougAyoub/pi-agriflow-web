<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Annonce;
use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * @return Annonce[]
     */
    public function searchPublic(?string $keyword = null, ?AnnonceType $type = null): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->andWhere('a.statut = :statut')
            ->setParameter('statut', AnnonceStatut::DISPONIBLE)
            ->orderBy('a.createdAt', 'DESC');

        if (null !== $keyword && '' !== trim($keyword)) {
            $queryBuilder
                ->andWhere('a.titre LIKE :keyword OR a.categorie LIKE :keyword OR a.localisation LIKE :keyword')
                ->setParameter('keyword', '%'.trim($keyword).'%');
        }

        if (null !== $type) {
            $queryBuilder
                ->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Annonce[]
     */
    public function findByOwnerId(int $ownerId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.proprietaireId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByOwnerId(int $ownerId): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.proprietaireId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
