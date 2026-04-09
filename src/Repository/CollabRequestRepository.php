<?php

namespace App\Repository;

use App\Entity\CollabRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class CollabRequestRepository extends ServiceEntityRepository
{
    private const SORT_INDEX = [
        'date_desc' => ['r.createdAt', 'DESC'],
        'date_asc' => ['r.createdAt', 'ASC'],
        'salary_desc' => ['r.salary', 'DESC'],
        'salary_asc' => ['r.salary', 'ASC'],
        'title_asc' => ['r.title', 'ASC'],
    ];

    private const SORT_MY_REQUESTS = [
        'date_desc' => ['r.createdAt', 'DESC'],
        'date_asc' => ['r.createdAt', 'ASC'],
        'title_asc' => ['r.title', 'ASC'],
        'salary_desc' => ['r.salary', 'DESC'],
        'salary_asc' => ['r.salary', 'ASC'],
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollabRequest::class);
    }

    public function findApproved(): array
    {
        return $this->findApprovedFiltered(null, 'date_desc');
    }

    public function findApprovedFiltered(?string $location, string $sortKey): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', 'APPROVED');

        if ($location !== null && $location !== '') {
            $qb->andWhere('r.location LIKE :loc')
                ->setParameter('loc', '%' . $location . '%');
        }

        [$field, $dir] = self::SORT_INDEX[$sortKey] ?? self::SORT_INDEX['date_desc'];
        $qb->orderBy($field, $dir);

        return $qb->getQuery()->getResult();
    }

    public function findByRequester($requester): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.requester = :requester')
            ->setParameter('requester', $requester)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', $status)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function search(string $keyword): array
    {
        return $this->searchFiltered($keyword, null, 'date_desc');
    }

    public function searchFiltered(string $keyword, ?string $location, string $sortKey): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.title LIKE :kw OR r.description LIKE :kw OR r.location LIKE :kw')
            ->andWhere('r.status = :status')
            ->setParameter('kw', '%' . $keyword . '%')
            ->setParameter('status', 'APPROVED');

        if ($location !== null && $location !== '') {
            $qb->andWhere('r.location LIKE :loc')
                ->setParameter('loc', '%' . $location . '%');
        }

        [$field, $dir] = self::SORT_INDEX[$sortKey] ?? self::SORT_INDEX['date_desc'];
        $qb->orderBy($field, $dir);

        return $qb->getQuery()->getResult();
    }

    public function findByRequesterFiltered($requester, ?string $status, string $sortKey): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.requester = :requester')
            ->setParameter('requester', $requester);

        if ($status !== null && $status !== '' && strtoupper($status) !== 'ALL') {
            $qb->andWhere('r.status = :st')
                ->setParameter('st', strtoupper($status));
        }

        [$field, $dir] = self::SORT_MY_REQUESTS[$sortKey] ?? self::SORT_MY_REQUESTS['date_desc'];
        $qb->orderBy($field, $dir);

        return $qb->getQuery()->getResult();
    }

    public function save(CollabRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CollabRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Pagination des demandes visibles sur /collab (approuvées uniquement).
     *
     * @return Paginator<CollabRequest>
     */
    public function paginateOpen(int $page, int $perPage = 9): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', 'APPROVED')
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return new Paginator($qb);
    }

    /**
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
            $qb->andWhere('r.status = :status')->setParameter('status', $status);
        }

        return new Paginator($qb);
    }

    public function findWithApplications(int $id): ?CollabRequest
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.applications', 'a')
            ->addSelect('a')
            ->leftJoin('a.candidate', 'c')
            ->addSelect('c')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
