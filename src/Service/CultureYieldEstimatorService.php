<?php

namespace App\Service;

class CultureYieldEstimatorService
{
    private const TYPE_COEFFICIENTS = [
        'BLE' => 0.6,
        'ORGE' => 0.5,
        'MAIS' => 0.8,
        'POMME_DE_TERRE' => 2.5,
        'TOMATE' => 3.0,
        'OLIVIER' => 0.7,
        'AGRUMES' => 1.2,
        'VIGNE' => 1.0,
        'PASTECQUE' => 2.0,
        'FRAISE' => 1.8,
        'LEGUMES' => 1.5,
        'AUTRE' => 1.0,
    ];

    /**
     * @return array<string, float>
     */
    public function getCoefficients(): array
    {
        return self::TYPE_COEFFICIENTS;
    }

    public function estimate(?string $typeCulture, ?float $superficie): float
    {
        $normalizedType = strtoupper(trim((string) $typeCulture));
        $normalizedSurface = max(0, (float) ($superficie ?? 0));
        $coefficient = $this->getCoefficients()[$normalizedType] ?? $this->getCoefficients()['AUTRE'];

        return round($coefficient * $normalizedSurface, 2);
    }
}
