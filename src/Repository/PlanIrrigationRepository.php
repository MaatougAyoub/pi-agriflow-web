<?php
namespace App\Repository;

use App\Entity\PlanIrrigation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlanIrrigationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanIrrigation::class);
    }

    /** Tous les plans avec leur culture chargée (évite les requêtes N+1) */
    public function findAllWithCulture(): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.culture', 'c')
            ->addSelect('c')
            ->orderBy('p.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Un plan avec ses jours et sa culture */
    public function findOneWithJours(int $id): ?PlanIrrigation
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.culture', 'c')
            ->leftJoin('p.jours', 'j')
            ->addSelect('c', 'j')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}