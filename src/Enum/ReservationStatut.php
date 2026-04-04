<?php

declare(strict_types=1);

namespace App\Enum;

enum ReservationStatut: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case ACCEPTEE = 'ACCEPTEE';
    case REFUSEE = 'REFUSEE';
    case EN_COURS = 'EN_COURS';
    case TERMINEE = 'TERMINEE';
    case ANNULEE = 'ANNULEE';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::ACCEPTEE => 'Acceptee',
            self::REFUSEE => 'Refusee',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminee',
            self::ANNULEE => 'Annulee',
        };
    }
}
