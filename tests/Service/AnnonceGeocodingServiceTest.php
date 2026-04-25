<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Annonce;
use App\Service\AnnonceGeocodingService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class AnnonceGeocodingServiceTest extends TestCase
{
    public function testEnrichAnnonceStoresCoordinatesWhenApiMatches(): void
    {
        $annonce = (new Annonce())
            ->setLocalisation('Nabeul');

        $service = new AnnonceGeocodingService(
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

        $result = $service->enrichAnnonce($annonce);

        self::assertSame('matched', $result['status']);
        self::assertSame(36.4513, $annonce->getLatitude());
        self::assertSame(10.735, $annonce->getLongitude());
        self::assertSame('Nabeul, Tunisie', $annonce->getLocalisationNormalisee());
    }

    public function testEnrichAnnonceReturnsFallbackWhenApiFindsNothing(): void
    {
        $annonce = (new Annonce())
            ->setLocalisation('Zone introuvable')
            ->setLatitude(1.0)
            ->setLongitude(2.0)
            ->setLocalisationNormalisee('Ancienne valeur');

        $service = new AnnonceGeocodingService(
            new MockHttpClient([new MockResponse('[]')]),
            new NullLogger(),
            'https://nominatim.openstreetmap.org'
        );

        $result = $service->enrichAnnonce($annonce);

        self::assertSame('fallback', $result['status']);
        self::assertNull($annonce->getLatitude());
        self::assertNull($annonce->getLongitude());
        self::assertNull($annonce->getLocalisationNormalisee());
    }

    public function testEnrichAnnonceReturnsFallbackWhenApiFails(): void
    {
        $annonce = (new Annonce())
            ->setLocalisation('Tunis');

        $service = new AnnonceGeocodingService(
            new MockHttpClient(static function (): never {
                throw new \RuntimeException('API down');
            }),
            new NullLogger(),
            'https://nominatim.openstreetmap.org'
        );

        $result = $service->enrichAnnonce($annonce);

        self::assertSame('fallback', $result['status']);
        self::assertStringContainsString('geocodage OpenStreetMap', (string) $result['message']);
        self::assertNull($annonce->getLatitude());
    }
}
