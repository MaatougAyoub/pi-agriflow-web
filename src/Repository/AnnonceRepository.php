<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Annonce;
use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
        return $this->createPublicSearchQueryBuilder($keyword, $type)
            ->getQuery()
            ->getResult();
    }

    public function createPublicSearchQueryBuilder(?string $keyword = null, ?AnnonceType $type = null): QueryBuilder
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

        return $queryBuilder;
    }

    /**
     * @return Annonce[]
     */
    public function findByOwnerId(int $ownerId): array
    {
        return $this->createByOwnerIdQueryBuilder($ownerId)
            ->getQuery()
            ->getResult();
    }

    public function createByOwnerIdQueryBuilder(int $ownerId): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.proprietaireId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('a.createdAt', 'DESC')
        ;
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

    /**
     * @return Annonce[]
     */
    public function findSimilarPublic(Annonce $annonce, int $limit = 4): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id != :annonceId')
            ->andWhere('a.statut = :statut')
            ->andWhere('a.type = :type')
            ->andWhere('a.categorie = :categorie')
            ->setParameter('annonceId', $annonce->getId())
            ->setParameter('statut', AnnonceStatut::DISPONIBLE)
            ->setParameter('type', $annonce->getType())
            ->setParameter('categorie', $annonce->getCategorie())
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
