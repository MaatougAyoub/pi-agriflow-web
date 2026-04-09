<?php

namespace App\Repository;

use App\Entity\Culture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Culture>
 */
class CultureRepository extends ServiceEntityRepository
{
    private const ALLOWED_SORT_FIELDS = [
        'nom' => 'c.nom',
        'superficie' => 'c.superficie',
        'recolte_estime' => 'c.recolte_estime',
        'date_creation' => 'c.date_creation',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Culture::class);
    }

    /**
     * @return Culture[]
     */
    public function findByProprietaireId(int $proprietaireId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.proprietaire_id = :proprietaireId')
            ->setParameter('proprietaireId', $proprietaireId)
            ->orderBy('c.date_creation', 'DESC')
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<string, string|null> $criteria
     * @return Culture[]
     */
    public function findFilteredForProprietaire(int $proprietaireId, array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('c.proprietaire_id = :proprietaireId')
            ->setParameter('proprietaireId', $proprietaireId);

        $search = trim((string) ($criteria['search'] ?? ''));
        if ('' !== $search) {
            $queryBuilder
                ->andWhere('LOWER(COALESCE(c.nom, \'\')) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        $typeCulture = trim((string) ($criteria['type_culture'] ?? ''));
        if ('' !== $typeCulture) {
            $queryBuilder
                ->andWhere('c.type_culture = :typeCulture')
                ->setParameter('typeCulture', $typeCulture);
        }

        $parcelleId = trim((string) ($criteria['parcelle_id'] ?? ''));
        if ('' !== $parcelleId && ctype_digit($parcelleId)) {
            $queryBuilder
                ->andWhere('c.parcelle_id = :parcelleId')
                ->setParameter('parcelleId', (int) $parcelleId);
        }

        $etat = trim((string) ($criteria['etat'] ?? ''));
        if ('' !== $etat) {
            $queryBuilder
                ->andWhere('c.etat = :etat')
                ->setParameter('etat', $etat);
        }

        $sort = (string) ($criteria['sort'] ?? 'date_creation');
        $direction = strtolower((string) ($criteria['direction'] ?? 'desc'));
        $sortField = self::ALLOWED_SORT_FIELDS[$sort] ?? self::ALLOWED_SORT_FIELDS['date_creation'];
        $sortDirection = 'asc' === $direction ? 'ASC' : 'DESC';

        return $queryBuilder
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string[]
     */
    public function findTypeCultureChoicesForProprietaire(int $proprietaireId): array
    {
        return $this->findDistinctStringValuesForProprietaire($proprietaireId, 'c.type_culture', 'typeCulture');
    }

    /**
     * @return string[]
     */
    public function findEtatChoicesForProprietaire(int $proprietaireId): array
    {
        return $this->findDistinctStringValuesForProprietaire($proprietaireId, 'c.etat', 'etat');
    }

    /**
     * @return string[]
     */
    private function findDistinctStringValuesForProprietaire(int $proprietaireId, string $field, string $alias): array
    {
        $results = $this->createQueryBuilder('c')
            ->select(sprintf('DISTINCT %s AS %s', $field, $alias))
            ->andWhere('c.proprietaire_id = :proprietaireId')
            ->andWhere(sprintf('%s IS NOT NULL', $field))
            ->andWhere(sprintf('%s != :emptyValue', $field))
            ->setParameter('proprietaireId', $proprietaireId)
            ->setParameter('emptyValue', '')
            ->orderBy($field, 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): string => (string) $row[$alias], $results);
    }

    public function findOneForProprietaire(int $id, int $proprietaireId): ?Culture
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->andWhere('c.proprietaire_id = :proprietaireId')
            ->setParameter('id', $id)
            ->setParameter('proprietaireId', $proprietaireId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Culture[]
     */
    public function findPurchasedByAcheteurId(int $acheteurId): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.utilisateur', 'acheteur')
            ->addSelect('acheteur')
            ->andWhere('acheteur.id = :acheteurId')
            ->setParameter('acheteurId', $acheteurId)
            ->orderBy('c.date_vente', 'DESC')
            ->addOrderBy('c.date_creation', 'DESC')
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Culture[]
     */
    public function findPublishedForMarketplace(int $viewerId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.proprietaire_id != :viewerId')
            ->andWhere('c.etat = :etat')
            ->andWhere('c.utilisateur IS NULL')
            ->setParameter('viewerId', $viewerId)
            ->setParameter('etat', Culture::ETAT_EN_VENTE)
            ->orderBy('c.date_publication', 'DESC')
            ->addOrderBy('c.date_creation', 'DESC')
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<string, string|null> $criteria
     * @return Culture[]
     */
    public function findFilteredForAdmin(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $search = trim((string) ($criteria['search'] ?? ''));
        if ('' !== $search) {
            $queryBuilder
                ->andWhere('LOWER(COALESCE(c.nom, \'\')) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        $typeCulture = trim((string) ($criteria['type_culture'] ?? ''));
        if ('' !== $typeCulture) {
            $queryBuilder
                ->andWhere('c.type_culture = :typeCulture')
                ->setParameter('typeCulture', $typeCulture);
        }

        $parcelleId = trim((string) ($criteria['parcelle_id'] ?? ''));
        if ('' !== $parcelleId && ctype_digit($parcelleId)) {
            $queryBuilder
                ->andWhere('c.parcelle_id = :parcelleId')
                ->setParameter('parcelleId', (int) $parcelleId);
        }

        $etat = trim((string) ($criteria['etat'] ?? ''));
        if ('' !== $etat) {
            $queryBuilder
                ->andWhere('c.etat = :etat')
                ->setParameter('etat', $etat);
        }

        $sort = (string) ($criteria['sort'] ?? 'date_creation');
        $direction = strtolower((string) ($criteria['direction'] ?? 'desc'));
        $sortField = self::ALLOWED_SORT_FIELDS[$sort] ?? self::ALLOWED_SORT_FIELDS['date_creation'];
        $sortDirection = 'asc' === $direction ? 'ASC' : 'DESC';

        return $queryBuilder
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string[]
     */
    public function findTypeCultureChoicesForAdmin(): array
    {
        return $this->findDistinctStringValues('c.type_culture', 'typeCulture');
    }

    /**
     * @return string[]
     */
    public function findEtatChoicesForAdmin(): array
    {
        return $this->findDistinctStringValues('c.etat', 'etat');
    }

    /**
     * @return Culture[]
     */
    public function findByParcelleIdAndProprietaireId(int $parcelleId, int $proprietaireId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.parcelle_id = :parcelleId')
            ->andWhere('c.proprietaire_id = :proprietaireId')
            ->setParameter('parcelleId', $parcelleId)
            ->setParameter('proprietaireId', $proprietaireId)
            ->orderBy('c.date_creation', 'DESC')
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Culture[]
     */
    public function findByParcelleIdForAdmin(int $parcelleId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.parcelle_id = :parcelleId')
            ->setParameter('parcelleId', $parcelleId)
            ->orderBy('c.date_creation', 'DESC')
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getUsedSurfaceForParcelle(int $parcelleId, ?int $excludeCultureId = null): float
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COALESCE(SUM(c.superficie), 0)')
            ->andWhere('c.parcelle_id = :parcelleId')
            ->andWhere('(c.etat IS NULL OR c.etat != :recoltee)')
            ->setParameter('parcelleId', $parcelleId);
        $queryBuilder->setParameter('recoltee', Culture::ETAT_RECOLTEE);

        if (null !== $excludeCultureId) {
            $queryBuilder
                ->andWhere('c.id != :excludeCultureId')
                ->setParameter('excludeCultureId', $excludeCultureId);
        }

        return (float) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return string[]
     */
    private function findDistinctStringValues(string $field, string $alias): array
    {
        $results = $this->createQueryBuilder('c')
            ->select(sprintf('DISTINCT %s AS %s', $field, $alias))
            ->andWhere(sprintf('%s IS NOT NULL', $field))
            ->andWhere(sprintf('%s != :emptyValue', $field))
            ->setParameter('emptyValue', '')
            ->orderBy($field, 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): string => (string) $row[$alias], $results);
    }

    //    /**
    //     * @return Culture[] Returns an array of Culture objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Culture
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
