<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CollabRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollabRequest>
 */
class CollabRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollabRequest::class);
    }

    /**
     * Returns a paginated list of open requests, newest first.
     *
     * @return Paginator<CollabRequest>
     */
    public function paginateOpen(int $page, int $perPage = 9): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', CollabRequest::STATUS_OPEN)
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return new Paginator($qb);
    }

    /**
     * Paginate ALL requests for the back-office, with optional status filter.
     *
     * @return Paginator<CollabRequest>
     */
    public function paginateAll(int $page, int $perPage = 15, ?string $status = null): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.requester', 'u')
            ->addSelect('u')
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if ($status !== null && $status !== '') {
            $qb->where('r.status = :status')->setParameter('status', $status);
        }

        return new Paginator($qb);
    }

    /**
     * Returns requests belonging to a specific user.
     *
     * @return CollabRequest[]
     */
    public function findByRequester(int $requesterId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.requester = :uid')
            ->setParameter('uid', $requesterId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Loads a request with its applications in a single query (avoids N+1).
     */
    public function findWithApplications(int $id): ?CollabRequest
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.applications', 'a')
            ->leftJoin('a.candidate', 'c')
            ->addSelect('a', 'c')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
