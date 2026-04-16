<?php

namespace App\Model;

/**
 * DTO pour les prévisions météo quotidiennes (Open-Meteo).
 * Pas une entité Doctrine — simple objet de transfert.
 */
class DailyForecast
{
    private const WEATHER_LABELS = [
        0  => 'Ciel dégagé',
        1  => 'Principalement dégagé',
        2  => 'Partiellement nuageux',
        3  => 'Couvert',
        45 => 'Brouillard',
        48 => 'Brouillard givrant',
        51 => 'Bruine légère',
        53 => 'Bruine modérée',
        55 => 'Bruine dense',
        56 => 'Bruine verglaçante',
        57 => 'Bruine verglaçante dense',
        61 => 'Pluie légère',
        63 => 'Pluie modérée',
        65 => 'Pluie forte',
        66 => 'Pluie verglaçante',
        67 => 'Pluie verglaçante forte',
        71 => 'Neige légère',
        73 => 'Neige modérée',
        75 => 'Neige forte',
        77 => 'Grains de neige',
        80 => 'Averses légères',
        81 => 'Averses modérées',
        82 => 'Averses violentes',
        85 => 'Averses de neige légères',
        86 => 'Averses de neige fortes',
        95 => 'Orage',
        96 => 'Orage avec grêle légère',
        99 => 'Orage avec grêle forte',
    ];

    private const WEATHER_ICONS = [
        0 => '☀️', 1 => '🌤️', 2 => '⛅', 3 => '☁️',
        45 => '🌫️', 48 => '🌫️',
        51 => '🌦️', 53 => '🌦️', 55 => '🌧️',
        56 => '🌧️', 57 => '🌧️',
        61 => '🌦️', 63 => '🌧️', 65 => '🌧️',
        66 => '🌧️', 67 => '🌧️',
        71 => '🌨️', 73 => '🌨️', 75 => '❄️',
        77 => '❄️',
        80 => '🌦️', 81 => '🌧️', 82 => '⛈️',
        85 => '🌨️', 86 => '🌨️',
        95 => '⛈️', 96 => '⛈️', 99 => '⛈️',
    ];

    public function __construct(
        private \DateTimeInterface $date,
        private float $tempMin,
        private float $tempMax,
        private float $precipitationMm,
        private int $weatherCode,
    ) {}

    public function getDate(): \DateTimeInterface { return $this->date; }
    public function getTempMin(): float { return $this->tempMin; }
    public function getTempMax(): float { return $this->tempMax; }
    public function getPrecipitationMm(): float { return $this->precipitationMm; }
    public function getWeatherCode(): int { return $this->weatherCode; }

    public function getWeatherDescription(): string
    {
        return self::WEATHER_LABELS[$this->weatherCode] ?? 'Conditions variées';
    }

    public function getWeatherIcon(): string
    {
        return self::WEATHER_ICONS[$this->weatherCode] ?? '🌡️';
    }
}
