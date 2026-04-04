<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PlansIrrigationJour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlansIrrigationJour>
 */
class PlansIrrigationJourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlansIrrigationJour::class);
    }
}
