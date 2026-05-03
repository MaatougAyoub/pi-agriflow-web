<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Culture;

final class CultureManager
{
    public function canPublish(Culture $culture): bool
    {
        return Culture::ETAT_EN_COURS === $culture->getEtat();
    }
}
