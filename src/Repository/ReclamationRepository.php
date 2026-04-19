<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    /**
     * @return list<Reclamation>
     */
    public function searchWithUser(?string $query, ?string $category): array
    {
        return $this->createSearchWithUserQueryBuilder($query, $category)
            ->getQuery()
            ->getResult();
    }

    public function createSearchWithUserQueryBuilder(?string $query, ?string $category): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.utilisateur', 'u')
            ->addSelect('u')
            ->orderBy('r.dateCreation', 'DESC')
            ->addOrderBy('r.id', 'DESC');

        $normalizedQuery = trim((string) $query);
        if ($normalizedQuery !== '') {
            $normalizedQuery = mb_strtolower($normalizedQuery);
            $qb
                ->andWhere("LOWER(r.titre) LIKE :q OR LOWER(r.description) LIKE :q OR LOWER(r.categorie) LIKE :q OR LOWER(r.statut) LIKE :q OR LOWER(COALESCE(r.reponse, '')) LIKE :q OR LOWER(COALESCE(u.email, '')) LIKE :q OR LOWER(COALESCE(u.nom, '')) LIKE :q OR LOWER(COALESCE(u.prenom, '')) LIKE :q")
                ->setParameter('q', '%'.$normalizedQuery.'%');
        }

        $normalizedCategory = strtoupper(trim((string) $category));
        if ($normalizedCategory !== '' && $normalizedCategory !== 'TOUTES') {
            $qb
                ->andWhere('UPPER(r.categorie) = :category')
                ->setParameter('category', $normalizedCategory);
        }

        return $qb;
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('UPPER(r.statut) = :status')
            ->setParameter('status', 'EN_ATTENTE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findLatestPending(): ?Reclamation
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.utilisateur', 'u')
            ->addSelect('u')
            ->andWhere('UPPER(r.statut) = :status')
            ->setParameter('status', 'EN_ATTENTE')
            ->orderBy('r.dateCreation', 'DESC')
            ->addOrderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return list<Reclamation>
     */
    public function findNotificationHistoryForAdmin(int $limit = 50): array
    {
        $limit = max(1, min($limit, 200));

        return $this->createQueryBuilder('r')
            ->leftJoin('r.utilisateur', 'u')
            ->addSelect('u')
            ->andWhere('UPPER(COALESCE(u.role, :emptyRole)) != :adminRole')
            ->andWhere('UPPER(COALESCE(u.role, :emptyRole)) != :adminRolePrefixed')
            ->setParameter('emptyRole', '')
            ->setParameter('adminRole', 'ADMIN')
            ->setParameter('adminRolePrefixed', 'ROLE_ADMIN')
            ->orderBy('r.dateCreation', 'DESC')
            ->addOrderBy('r.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
