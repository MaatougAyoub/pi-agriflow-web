<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CollabApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollabApplication>
 */
class CollabApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollabApplication::class);
    }

    /**
     * Returns all applications submitted by a specific user.
     *
     * @return CollabApplication[]
     */
    public function findByCandidate(int $candidateId): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.request', 'r')
            ->addSelect('r')
            ->where('a.candidate = :uid')
            ->setParameter('uid', $candidateId)
            ->orderBy('a.appliedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns all applications for a specific collaboration request.
     *
     * @return CollabApplication[]
     */
    public function findByRequest(int $requestId): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.candidate', 'c')
            ->addSelect('c')
            ->where('a.request = :rid')
            ->setParameter('rid', $requestId)
            ->orderBy('a.appliedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Checks whether a candidate has already applied to a given request.
     */
    public function hasApplied(int $candidateId, int $requestId): bool
    {
        $count = (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.candidate = :cid AND a.request = :rid')
            ->setParameter('cid', $candidateId)
            ->setParameter('rid', $requestId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * Paginate ALL applications for back-office, with optional status filter.
     *
     * @return Paginator<CollabApplication>
     */
    public function paginateAll(int $page, int $perPage = 15, ?string $status = null): Paginator
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.request', 'r')
            ->leftJoin('a.candidate', 'c')
            ->addSelect('r', 'c')
            ->orderBy('a.appliedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if ($status !== null && $status !== '') {
            $qb->where('a.status = :status')->setParameter('status', $status);
        }

        return new Paginator($qb);
    }
}
