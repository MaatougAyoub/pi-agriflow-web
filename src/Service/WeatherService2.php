<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService2
{
    public function __construct(private HttpClientInterface $http) {}

    /**
     * @return array<string, mixed>
     */
    public function getForecast(float $lat, float $lon): array
    {
        $response = $this->http->request('GET',
            'https://api.open-meteo.com/v1/forecast', [
                'query' => [
                    'latitude'                    => $lat,
                    'longitude'                   => $lon,
                    'daily'                       => 'precipitation_sum,temperature_2m_max,relative_humidity_2m_max',
                    'timezone'                    => 'auto',
                    'forecast_days'               => 7,
                ]
            ]
        );
        return $response->toArray();
    }
}