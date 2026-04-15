<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Annonce;
use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use App\Service\AnnonceBusinessDiagnosticService;
use PHPUnit\Framework\TestCase;

final class AnnonceBusinessDiagnosticServiceTest extends TestCase
{
    public function testBuildForAnnonceReturnsPerfectScoreForCompleteAnnonce(): void
    {
        $annonce = $this->createAnnonce()
            ->setLatitude(36.8065)
            ->setLongitude(10.1815)
            ->setLocalisationNormalisee('Tunis, Tunisie')
            ->setImageUrl('https://cdn.agriflow.test/tracteur.jpg');

        $diagnostic = (new AnnonceBusinessDiagnosticService())->buildForAnnonce($annonce, [
            'weather' => ['available' => true],
            'airQuality' => ['available' => true],
        ]);

        self::assertSame(100, $diagnostic['score']);
        self::assertSame('Tres solide', $diagnostic['level']);
        self::assertSame('5%', $diagnostic['commissionRateLabel']);
        self::assertSame([], $diagnostic['advice']);
        self::assertTrue($diagnostic['weatherAvailable']);
        self::assertTrue($diagnostic['airQualityAvailable']);
    }

    public function testBuildForAnnonceReturnsAdviceForIncompleteAnnonce(): void
    {
        $annonce = $this->createAnnonce()
            ->setStatut(AnnonceStatut::VENDUE)
            ->setQuantiteDisponible(0)
            ->setImageUrl('https://example.com/tracteur.jpg');

        $diagnostic = (new AnnonceBusinessDiagnosticService())->buildForAnnonce($annonce);

        self::assertLessThan(60, $diagnostic['score']);
        self::assertSame('Incomplet', $diagnostic['level']);
        self::assertNotEmpty($diagnostic['advice']);
        self::assertFalse($diagnostic['weatherAvailable']);
        self::assertFalse($diagnostic['airQualityAvailable']);
    }

    private function createAnnonce(): Annonce
    {
        return (new Annonce())
            ->setTitre('Tracteur agricole')
            ->setDescription('Description assez longue pour presenter une annonce marketplace agricole fiable.')
            ->setType(AnnonceType::LOCATION)
            ->setStatut(AnnonceStatut::DISPONIBLE)
            ->setPrix(120)
            ->setCategorie('Materiel')
            ->setImageUrl('https://cdn.agriflow.test/image.jpg')
            ->setLocalisation('Tunis')
            ->setProprietaireId(4)
            ->setQuantiteDisponible(3)
            ->setUnitePrix('jour');
    }
}
