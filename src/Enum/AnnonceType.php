<?php

declare(strict_types=1);

namespace App\Enum;

enum AnnonceType: string
{
    case VENTE = 'VENTE';
    case LOCATION = 'LOCATION';

    public function label(): string
    {
        return match ($this) {
            self::VENTE => 'Vente',
            self::LOCATION => 'Location',
        };
    }
}
