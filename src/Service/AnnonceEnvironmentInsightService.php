<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Annonce;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AnnonceEnvironmentInsightService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(string:OPEN_METEO_FORECAST_BASE_URL)%')]
        private readonly string $forecastBaseUrl,
        #[Autowire('%env(string:OPEN_METEO_AIR_QUALITY_BASE_URL)%')]
        private readonly string $airQualityBaseUrl,
    ) {
    }

    /**
     * @return array{
     *     available: bool,
     *     source: string,
     *     weather: array<string, mixed>,
     *     airQuality: array<string, mixed>
     * }
     */
    public function buildForAnnonce(Annonce $annonce): array
    {
        $latitude = $annonce->getLatitude();
        $longitude = $annonce->getLongitude();

        $insights = [
            'available' => false,
            'source' => 'Open-Meteo',
            'weather' => $this->unavailable('Meteo indisponible sans coordonnees.'),
            'airQuality' => $this->unavailable('Qualite de l air indisponible sans coordonnees.'),
        ];

        if (null === $latitude || null === $longitude) {
            return $insights;
        }

        // api: Open-Meteo ykammel OpenStreetMap b meteo w qualite air mel coordonnees
        $insights['weather'] = $this->fetchForecast($latitude, $longitude);
        $insights['airQuality'] = $this->fetchAirQuality($latitude, $longitude);
        $insights['available'] = $insights['weather']['available'] || $insights['airQuality']['available'];

        return $insights;
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchForecast(float $latitude, float $longitude): array
    {
        try {
            $response = $this->httpClient->request('GET', rtrim($this->forecastBaseUrl, '/').'/v1/forecast', [
                'query' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,precipitation,weather_code,wind_speed_10m',
                    'timezone' => 'auto',
                    'forecast_days' => 1,
                ],
                'timeout' => 8,
            ]);

            $payload = $response->toArray(false);
            $current = is_array($payload['current'] ?? null) ? $payload['current'] : [];

            if ([] === $current) {
                return $this->unavailable('Meteo Open-Meteo indisponible pour cette localisation.');
            }

            $weatherCode = $this->toInt($current['weather_code'] ?? null);

            return [
                'available' => true,
                'temperature' => $this->toFloat($current['temperature_2m'] ?? null),
                'humidity' => $this->toInt($current['relative_humidity_2m'] ?? null),
                'precipitation' => $this->toFloat($current['precipitation'] ?? null),
                'windSpeed' => $this->toFloat($current['wind_speed_10m'] ?? null),
                'weatherCode' => $weatherCode,
                'label' => $this->weatherLabel($weatherCode),
                'message' => null,
            ];
        } catch (\Throwable $exception) {
            $this->logger->warning('Open-Meteo forecast indisponible.', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $exception->getMessage(),
            ]);

            return $this->unavailable('Meteo Open-Meteo temporairement indisponible.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchAirQuality(float $latitude, float $longitude): array
    {
        try {
            $response = $this->httpClient->request('GET', rtrim($this->airQualityBaseUrl, '/').'/v1/air-quality', [
                'query' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => 'european_aqi,pm10,pm2_5',
                    'timezone' => 'auto',
                ],
                'timeout' => 8,
            ]);

            $payload = $response->toArray(false);
            $current = is_array($payload['current'] ?? null) ? $payload['current'] : [];

            if ([] === $current) {
                return $this->unavailable('Qualite de l air indisponible pour cette localisation.');
            }

            $aqi = $this->toInt($current['european_aqi'] ?? null);

            return [
                'available' => true,
                'europeanAqi' => $aqi,
                'level' => $this->airQualityLevel($aqi),
                'pm10' => $this->toFloat($current['pm10'] ?? null),
                'pm25' => $this->toFloat($current['pm2_5'] ?? null),
                'message' => null,
            ];
        } catch (\Throwable $exception) {
            $this->logger->warning('Open-Meteo air quality indisponible.', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $exception->getMessage(),
            ]);

            return $this->unavailable('Qualite de l air Open-Meteo temporairement indisponible.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function unavailable(string $message): array
    {
        return [
            'available' => false,
            'message' => $message,
        ];
    }

    private function toFloat(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function toInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) round((float) $value) : null;
    }

    private function weatherLabel(?int $code): string
    {
        return match (true) {
            null === $code => 'Non disponible',
            0 === $code => 'Ciel clair',
            in_array($code, [1, 2, 3], true) => 'Partiellement nuageux',
            in_array($code, [45, 48], true) => 'Brouillard',
            in_array($code, [51, 53, 55, 56, 57], true) => 'Bruine',
            in_array($code, [61, 63, 65, 66, 67, 80, 81, 82], true) => 'Pluie',
            in_array($code, [71, 73, 75, 77, 85, 86], true) => 'Neige',
            in_array($code, [95, 96, 99], true) => 'Orage',
            default => 'Conditions variables',
        };
    }

    private function airQualityLevel(?int $aqi): string
    {
        return match (true) {
            null === $aqi => 'Non disponible',
            $aqi <= 20 => 'Bonne',
            $aqi <= 40 => 'Correcte',
            $aqi <= 60 => 'Moyenne',
            $aqi <= 80 => 'Mauvaise',
            $aqi <= 100 => 'Tres mauvaise',
            default => 'Extreme',
        };
    }
}
