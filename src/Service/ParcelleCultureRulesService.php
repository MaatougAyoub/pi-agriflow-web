<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Culture;
use App\Entity\Parcelle;
use App\Entity\Utilisateur;

final class ParcelleCultureRulesService
{
    /**
     * @param iterable<Culture> $cultures
     */
    public function canDeleteParcelle(Parcelle $parcelle, iterable $cultures): bool
    {
        foreach ($cultures as $culture) {
            if ($culture->getParcelleId() === $parcelle->getId() && !$culture->isRecoltee()) {
                return false;
            }
        }

        return true;
    }

    public function canBuyCulture(Culture $culture, Utilisateur $acheteur): bool
    {
        return $culture->canBeBoughtBy($acheteur->getId());
    }

    public function canPublishCulture(Culture $culture): bool
    {
        return $culture->isEnCours() && !$culture->hasAcheteur();
    }
}
