<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\MarketplaceGeocodeAnnoncesCommand;
use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use App\Service\AnnonceGeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class MarketplaceGeocodeAnnoncesCommandTest extends TestCase
{
    public function testExecuteWithIdAndDryRunDoesNotFlush(): void
    {
        $annonce = $this->createAnnonce(19, 'Nabeul');

        $repository = $this->createMock(AnnonceRepository::class);
        $repository->expects(self::once())
            ->method('find')
            ->with(19)
            ->willReturn($annonce);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('flush');

        $geocodingService = new AnnonceGeocodingService(
            new MockHttpClient([
                new MockResponse(json_encode([[
                    'lat' => '36.4513',
                    'lon' => '10.7350',
                    'display_name' => 'Nabeul, Tunisie',
                ]]) ?: '[]'),
            ]),
            new NullLogger(),
            'https://nominatim.openstreetmap.org'
        );

        $tester = new CommandTester(new MarketplaceGeocodeAnnoncesCommand($repository, $geocodingService, $entityManager));
        $statusCode = $tester->execute([
            '--id' => '19',
            '--dry-run' => true,
        ]);

        self::assertSame(Command::SUCCESS, $statusCode);
        self::assertStringContainsString('Dry-run: aucune modification sauvegardee.', $tester->getDisplay());
        self::assertNull($annonce->getLatitude());
        self::assertNull($annonce->getLongitude());
    }

    public function testExecuteWithLimitFlushesGeocodedAnnouncements(): void
    {
        $annonce = $this->createAnnonce(23, 'Tunis');

        $query = $this->createMock(Query::class);
        $query->expects(self::once())
            ->method('getResult')
            ->willReturn([$annonce]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('orderBy')->willReturnSelf();
        $queryBuilder->method('setMaxResults')->with(1)->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);

        $repository = $this->createMock(AnnonceRepository::class);
        $repository->expects(self::once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('flush');

        $geocodingService = new AnnonceGeocodingService(
            new MockHttpClient([
                new MockResponse(json_encode([[
                    'lat' => '36.8065',
                    'lon' => '10.1815',
                    'display_name' => 'Tunis, Tunisie',
                ]]) ?: '[]'),
            ]),
            new NullLogger(),
            'https://nominatim.openstreetmap.org'
        );

        $tester = new CommandTester(new MarketplaceGeocodeAnnoncesCommand($repository, $geocodingService, $entityManager));
        $statusCode = $tester->execute([
            '--limit' => '1',
        ]);

        self::assertSame(Command::SUCCESS, $statusCode);
        self::assertStringContainsString('1 annonce(s) geocodee(s).', $tester->getDisplay());
        self::assertSame(36.8065, $annonce->getLatitude());
    }

    public function testExecuteReturnsSuccessWhenNoAnnonceMatches(): void
    {
        $repository = $this->createMock(AnnonceRepository::class);
        $repository->expects(self::once())
            ->method('find')
            ->with(404)
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('flush');

        $tester = new CommandTester(new MarketplaceGeocodeAnnoncesCommand(
            $repository,
            new AnnonceGeocodingService(new MockHttpClient(), new NullLogger(), 'https://nominatim.openstreetmap.org'),
            $entityManager
        ));
        $statusCode = $tester->execute([
            '--id' => '404',
        ]);

        self::assertSame(Command::SUCCESS, $statusCode);
        self::assertStringContainsString('Aucune annonce Marketplace a geocoder.', $tester->getDisplay());
    }

    private function createAnnonce(int $id, string $localisation): Annonce
    {
        $annonce = (new Annonce())
            ->setTitre('Annonce test')
            ->setDescription('Description assez longue pour geocoder une annonce dans un test de commande Marketplace.')
            ->setPrix(120)
            ->setCategorie('Materiel')
            ->setImageUrl('https://example.com/image.jpg')
            ->setLocalisation($localisation)
            ->setProprietaireId(7)
            ->setQuantiteDisponible(2)
            ->setUnitePrix('jour');

        $property = new \ReflectionProperty($annonce, 'id');
        $property->setValue($annonce, $id);

        return $annonce;
    }
}
