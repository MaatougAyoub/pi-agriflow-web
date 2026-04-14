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
}
