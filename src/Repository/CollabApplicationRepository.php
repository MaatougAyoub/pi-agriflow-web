<?php

<<<<<<< HEAD
declare(strict_types=1);

=======
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
namespace App\Repository;

use App\Entity\CollabApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
<<<<<<< HEAD
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollabApplication>
 */
=======
use Doctrine\Persistence\ManagerRegistry;

>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
class CollabApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollabApplication::class);
    }

<<<<<<< HEAD
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
=======
    private const SORT_MY_APPLICATIONS = [
        'date_desc' => ['a.appliedAt', 'DESC'],
        'date_asc' => ['a.appliedAt', 'ASC'],
        'status_asc' => ['a.status', 'ASC'],
    ];

    /**
     * Candidatures d'un utilisateur
     */
    public function findByCandidate($candidate): array
    {
        return $this->findByCandidateFiltered($candidate, null, 'date_desc');
    }

    /**
     * Candidatures d'un utilisateur avec filtre statut et tri
     */
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

    /**
     * Candidatures pour une demande spécifique
     */
    public function findByRequest($request): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.request = :request')
            ->setParameter('request', $request)
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
            ->orderBy('a.appliedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
<<<<<<< HEAD
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
=======
     * Compter les candidatures pour une demande
     */
    public function countByRequest($request): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.request = :request')
            ->setParameter('request', $request)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Vérifier si un candidat a déjà postulé
     */
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
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    }
}
