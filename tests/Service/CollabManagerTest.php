<?php

namespace App\Tests\Service;

use App\Entity\CollabRequest;
use App\Service\CollabManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CollabManagerTest extends TestCase
{
    /**
     * Teste une demande valide.
     */
    public function testValidCollabRequest(): void
    {
        $request = new CollabRequest();
        $request->setTitle('Récolte de printemps');
        $request->setSalary(100.0);
        $request->setStartDate(new \DateTime('2026-05-01'));
        $request->setEndDate(new \DateTime('2026-05-15'));

        $manager = new CollabManager();
        
        $this->assertTrue($manager->validate($request));
    }

    /**
     * Teste le rejet si le titre est vide.
     */
    public function testCollabRequestWithoutTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Le titre est obligatoire');

        $request = new CollabRequest();
        // Titre non défini ou vide
        $request->setSalary(100.0);

        $manager = new CollabManager();
        $manager->validate($request);
    }

    /**
     * Teste le rejet si le salaire est négatif.
     */
    public function testCollabRequestWithNegativeSalary(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Le salaire doit être positif');

        $request = new CollabRequest();
        $request->setTitle('Titre valide');
        $request->setSalary(-10.0);

        $manager = new CollabManager();
        $manager->validate($request);
    }

    /**
     * Teste le rejet si les dates sont incohérentes.
     */
    public function testCollabRequestWithInvalidDates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La date de fin doit être après la date de début');

        $request = new CollabRequest();
        $request->setTitle('Titre valide');
        $request->setSalary(50.0);
        $request->setStartDate(new \DateTime('2026-06-01'));
        $request->setEndDate(new \DateTime('2026-05-01')); // Avant le début

        $manager = new CollabManager();
        $manager->validate($request);
    }
}
