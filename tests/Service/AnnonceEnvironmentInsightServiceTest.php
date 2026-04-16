<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Annonce;
use App\Service\AnnonceEnvironmentInsightService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class AnnonceEnvironmentInsightServiceTest extends TestCase
{
    public function testBuildForAnnonceReturnsForecastAndAirQuality(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(json_encode([
                'current' => [
                    'temperature_2m' => 24.5,
                    'relative_humidity_2m' => 62,
                    'precipitation' => 0.0,
                    'weather_code' => 0,
                    'wind_speed_10m' => 11.2,
                ],
            ]) ?: '{}'),
            new MockResponse(json_encode([
                'current' => [
                    'european_aqi' => 35,
                    'pm10' => 18.4,
                    'pm2_5' => 9.7,
                ],
            ]) ?: '{}'),
        ]);
        $annonce = (new Annonce())
            ->setLatitude(36.8065)
            ->setLongitude(10.1815);

        $result = (new AnnonceEnvironmentInsightService(
            $httpClient,
            new NullLogger(),
            'https://api.open-meteo.com',
            'https://air-quality-api.open-meteo.com'
        ))->buildForAnnonce($annonce);

        self::assertTrue($result['available']);
        self::assertTrue($result['weather']['available']);
        self::assertSame(24.5, $result['weather']['temperature']);
        self::assertSame('Ciel clair', $result['weather']['label']);
        self::assertTrue($result['airQuality']['available']);
        self::assertSame(35, $result['airQuality']['europeanAqi']);
        self::assertSame('Correcte', $result['airQuality']['level']);
    }

    public function testBuildForAnnonceSkipsExternalCallsWithoutCoordinates(): void
    {
        $annonce = new Annonce();

        $result = (new AnnonceEnvironmentInsightService(
            new MockHttpClient(),
            new NullLogger(),
            'https://api.open-meteo.com',
            'https://air-quality-api.open-meteo.com'
        ))->buildForAnnonce($annonce);

        self::assertFalse($result['available']);
        self::assertFalse($result['weather']['available']);
        self::assertFalse($result['airQuality']['available']);
    }
}
