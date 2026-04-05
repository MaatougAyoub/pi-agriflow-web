<?php

<<<<<<< HEAD
declare(strict_types=1);

=======
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
namespace App\Repository;

use App\Entity\CollabRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
<<<<<<< HEAD
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollabRequest>
 */
=======
use Doctrine\Persistence\ManagerRegistry;

>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
class CollabRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollabRequest::class);
    }

<<<<<<< HEAD
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
=======
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

    /**
     * Trouver toutes les demandes approuvées (visibles publiquement)
     */
    public function findApproved(): array
    {
        return $this->findApprovedFiltered(null, 'date_desc');
    }

    /**
     * Demandes approuvées avec filtre lieu et tri
     */
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

    /**
     * Trouver les demandes d'un utilisateur (par requester)
     */
    public function findByRequester($requester): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.requester = :requester')
            ->setParameter('requester', $requester)
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
<<<<<<< HEAD
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
=======
     * Trouver par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', $status)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche par mot-clé (titre ou description)
     */
    public function search(string $keyword): array
    {
        return $this->searchFiltered($keyword, null, 'date_desc');
    }

    /**
     * Recherche approuvées avec filtre lieu et tri
     */
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

    /**
     * Demandes publiées par un utilisateur (filtre statut + tri)
     *
     * @param object $requester Utilisateur
     */
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
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    }
}
