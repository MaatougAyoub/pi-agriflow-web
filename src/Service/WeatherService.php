<?php

namespace App\Service;

use App\Model\DailyForecast;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Récupère les prévisions météo via l'API Open-Meteo (gratuit, sans clé API).
 * Port de WeatherService.java du projet JavaFX.
 */
class WeatherService
{
    private const BASE_URL = 'https://api.open-meteo.com/v1/forecast';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {}

    /**
     * Prévisions quotidiennes entre $start et $end pour la localisation donnée.
     *
     * @return DailyForecast[]
     */
    public function getForecast(?float $latitude, ?float $longitude, ?\DateTimeInterface $start, ?\DateTimeInterface $end): array
    {
        if ($latitude === null || $longitude === null || $start === null || $end === null) {
            return [];
        }
        if ($end < $start) {
            return [];
        }

        try {
            $response = $this->httpClient->request('GET', self::BASE_URL, [
                'query' => [
                    'latitude'      => round($latitude, 4),
                    'longitude'     => round($longitude, 4),
                    'daily'         => 'temperature_2m_max,temperature_2m_min,precipitation_sum,weathercode',
                    'forecast_days' => 16,
                    'timezone'      => 'auto',
                ],
                'timeout' => 10,
            ]);

            if ($response->getStatusCode() !== 200) {
                return [];
            }

            $data = $response->toArray();
            $daily = $data['daily'] ?? null;
            if ($daily === null || !isset($daily['time'])) {
                return [];
            }

            $forecasts = [];
            $n = count($daily['time']);
            for ($i = 0; $i < $n; $i++) {
                $dateStr = $daily['time'][$i] ?? null;
                if ($dateStr === null) {
                    continue;
                }
                $date = new \DateTime($dateStr);

                // Ne garder que les jours dans la période de travail [start, end]
                if ($date < $start || $date > $end) {
                    continue;
                }

                $forecasts[] = new DailyForecast(
                    date: $date,
                    tempMin: (float) ($daily['temperature_2m_min'][$i] ?? 0),
                    tempMax: (float) ($daily['temperature_2m_max'][$i] ?? 0),
                    precipitationMm: (float) ($daily['precipitation_sum'][$i] ?? 0),
                    weatherCode: (int) ($daily['weathercode'][$i] ?? 0),
                );
            }

            return $forecasts;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Retourne une évaluation des risques météo basés sur les prévisions.
     */
    public function getRiskAssessment(array $forecasts): array
    {
        $risks = [];
        foreach ($forecasts as $f) {
            if ($f->getPrecipitationMm() > 5) {
                $risks[] = sprintf("⚠️ Forte pluie prévue le %s (%.1f mm). Travaux extérieurs déconseillés.", $f->getDate()->format('d/m'), $f->getPrecipitationMm());
            }
            if ($f->getTempMax() > 38) {
                $risks[] = sprintf("🔥 Forte chaleur prévue le %s (%d°C). Prévoyez de l'eau et des pauses.", $f->getDate()->format('d/m'), $f->getTempMax());
            }
            if ($f->getWeatherCode() >= 80) { // Orages / Grosses averses
                $risks[] = sprintf("⚡ Risque d'orages/averses le %s. Soyez vigilant.", $f->getDate()->format('d/m'));
            }
        }
        return array_unique($risks);
    }
}
