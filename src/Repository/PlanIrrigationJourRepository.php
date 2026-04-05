<?php
namespace App\Repository;

use App\Entity\PlanIrrigationJour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlanIrrigationJourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanIrrigationJour::class);
    }

    /** Jours d'un plan pour une semaine donnée (date du lundi) */
    public function findByPlanAndSemaine(int $planId, \DateTimeInterface $lundi): array
    {
        return $this->createQueryBuilder('j')
            ->where('j.planIrrigation = :planId')
            ->andWhere('j.dateSemaine = :lundi')
            ->setParameter('planId', $planId)
            ->setParameter('lundi', $lundi->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }
}