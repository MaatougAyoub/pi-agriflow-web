<?php

namespace App\Service;

use App\Entity\Culture;
use App\Entity\Parcelle;
use App\Repository\CultureRepository;

class ParcelleCultureSurfaceService
{
    public function __construct(
        private readonly CultureRepository $cultureRepository,
    ) {
    }

    public function getAvailableSurfaceForParcelle(Parcelle $parcelle, ?int $excludeCultureId = null): float
    {
        $parcelleSurface = (float) ($parcelle->getSuperficie() ?? 0);
        $usedSurface = $this->cultureRepository->getUsedSurfaceForParcelle($parcelle->getId(), $excludeCultureId);
        $availableSurface = $parcelleSurface - $usedSurface;

        return max(0, round($availableSurface, 2));
    }

    public function canAssignSurface(Parcelle $parcelle, ?float $requestedSurface, ?int $excludeCultureId = null): bool
    {
        if (null === $requestedSurface) {
            return true;
        }

        return $requestedSurface <= $this->getAvailableSurfaceForParcelle($parcelle, $excludeCultureId);
    }

    public function createSurfaceExceededMessage(Parcelle $parcelle, ?int $excludeCultureId = null): string
    {
        $availableSurface = $this->getAvailableSurfaceForParcelle($parcelle, $excludeCultureId);

        return sprintf(
            'La superficie saisie dépasse la surface disponible pour cette parcelle. Maximum disponible: %.2f m².',
            $availableSurface
        );
    }

    /**
     * @param Parcelle[] $parcelles
     * @return array<int, float>
     */
    public function buildAvailableSurfaceMap(array $parcelles, ?Culture $culture = null): array
    {
        $availableSurfaces = [];
        $excludeCultureId = $culture?->getId();

        foreach ($parcelles as $parcelle) {
            $parcelleId = $parcelle->getId();

            if (null === $parcelleId) {
                continue;
            }

            $availableSurfaces[$parcelleId] = $this->getAvailableSurfaceForParcelle($parcelle, $excludeCultureId);
        }

        return $availableSurfaces;
    }
}
