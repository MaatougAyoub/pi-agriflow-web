<?php

namespace App\Repository;

use App\Entity\CollabApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class CollabApplicationRepository extends ServiceEntityRepository
{
    private const SORT_MY_APPLICATIONS = [
        'date_desc' => ['a.appliedAt', 'DESC'],
        'date_asc' => ['a.appliedAt', 'ASC'],
        'status_asc' => ['a.status', 'ASC'],
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollabApplication::class);
    }

    public function findByCandidate($candidate): array
    {
        return $this->findByCandidateFiltered($candidate, null, 'date_desc');
    }

    public function findByCandidateFiltered($candidate, ?string $status, string $sortKey): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.candidate = :candidate')
            ->setParameter('candidate', $candidate);

        if ($status !== null && $status !== '' && strtoupper($status) !== 'ALL') {
            $qb->andWhere('a.status = :st')
                ->setParameter('st', strtoupper($status));
        }

        [$field, $dir] = self::SORT_MY_APPLICATIONS[$sortKey] ?? self::SORT_MY_APPLICATIONS['date_desc'];
        $qb->orderBy($field, $dir);

        return $qb->getQuery()->getResult();
    }

    public function findByRequest($request): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.request = :request')
            ->setParameter('request', $request)
            ->orderBy('a.appliedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByRequest($request): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.request = :request')
            ->setParameter('request', $request)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function hasApplied($candidate, $request): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.candidate = :candidate')
            ->andWhere('a.request = :request')
            ->setParameter('candidate', $candidate)
            ->setParameter('request', $request)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }

    public function save(CollabApplication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CollabApplication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Paginator<CollabApplication>
     */
    public function paginateAll(int $page, int $perPage = 15, ?string $status = null): Paginator
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.request', 'r')
            ->addSelect('r')
            ->leftJoin('a.candidate', 'c')
            ->addSelect('c')
            ->orderBy('a.appliedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if ($status !== null && $status !== '') {
            $qb->andWhere('a.status = :status')->setParameter('status', $status);
        }

        return new Paginator($qb);
    }
}
