<?php

namespace App\Repository;

use App\Entity\Parcelle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parcelle>
 */
class ParcelleRepository extends ServiceEntityRepository
{
    private const ALLOWED_SORT_FIELDS = [
        'nom' => 'p.nom',
        'superficie' => 'p.superficie',
        'date_creation' => 'p.date_creation',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parcelle::class);
    }

    /**
     * @return Parcelle[]
     */
    public function findByAgriculteurId(int $agriculteurId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('IDENTITY(p.agriculteur_id) = :agriculteurId')
            ->setParameter('agriculteurId', $agriculteurId)
            ->orderBy('p.date_creation', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<string, string|null> $criteria
     * @return Parcelle[]
     */
    public function findFilteredForAgriculteur(int $agriculteurId, array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere('IDENTITY(p.agriculteur_id) = :agriculteurId')
            ->setParameter('agriculteurId', $agriculteurId);

        $search = trim((string) ($criteria['search'] ?? ''));
        if ('' !== $search) {
            $queryBuilder
                ->andWhere('LOWER(COALESCE(p.nom, \'\')) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        $typeTerre = trim((string) ($criteria['type_terre'] ?? ''));
        if ('' !== $typeTerre) {
            $queryBuilder
                ->andWhere('p.type_terre = :typeTerre')
                ->setParameter('typeTerre', $typeTerre);
        }

        $sort = (string) ($criteria['sort'] ?? 'date_creation');
        $direction = strtolower((string) ($criteria['direction'] ?? 'desc'));
        $sortField = self::ALLOWED_SORT_FIELDS[$sort] ?? self::ALLOWED_SORT_FIELDS['date_creation'];
        $sortDirection = 'asc' === $direction ? 'ASC' : 'DESC';

        return $queryBuilder
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string[]
     */
    public function findTypeTerreChoicesForAgriculteur(int $agriculteurId): array
    {
        $results = $this->createQueryBuilder('p')
            ->select('DISTINCT p.type_terre AS typeTerre')
            ->andWhere('IDENTITY(p.agriculteur_id) = :agriculteurId')
            ->andWhere('p.type_terre IS NOT NULL')
            ->andWhere('p.type_terre != :emptyValue')
            ->setParameter('agriculteurId', $agriculteurId)
            ->setParameter('emptyValue', '')
            ->orderBy('p.type_terre', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): string => (string) $row['typeTerre'], $results);
    }

    public function findOneForAgriculteur(int $id, int $agriculteurId): ?Parcelle
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->andWhere('IDENTITY(p.agriculteur_id) = :agriculteurId')
            ->setParameter('id', $id)
            ->setParameter('agriculteurId', $agriculteurId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array<string, string|null> $criteria
     * @return Parcelle[]
     */
    public function findFilteredForAdmin(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $search = trim((string) ($criteria['search'] ?? ''));
        if ('' !== $search) {
            $queryBuilder
                ->andWhere('LOWER(COALESCE(p.nom, \'\')) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        $typeTerre = trim((string) ($criteria['type_terre'] ?? ''));
        if ('' !== $typeTerre) {
            $queryBuilder
                ->andWhere('p.type_terre = :typeTerre')
                ->setParameter('typeTerre', $typeTerre);
        }

        $sort = (string) ($criteria['sort'] ?? 'date_creation');
        $direction = strtolower((string) ($criteria['direction'] ?? 'desc'));
        $sortField = self::ALLOWED_SORT_FIELDS[$sort] ?? self::ALLOWED_SORT_FIELDS['date_creation'];
        $sortDirection = 'asc' === $direction ? 'ASC' : 'DESC';

        return $queryBuilder
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string[]
     */
    public function findTypeTerreChoicesForAdmin(): array
    {
        $results = $this->createQueryBuilder('p')
            ->select('DISTINCT p.type_terre AS typeTerre')
            ->andWhere('p.type_terre IS NOT NULL')
            ->andWhere('p.type_terre != :emptyValue')
            ->setParameter('emptyValue', '')
            ->orderBy('p.type_terre', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): string => (string) $row['typeTerre'], $results);
    }
}
