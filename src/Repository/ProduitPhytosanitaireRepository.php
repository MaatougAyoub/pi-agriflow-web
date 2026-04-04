<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProduitPhytosanitaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitPhytosanitaire>
 */
class ProduitPhytosanitaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitPhytosanitaire::class);
    }
}
