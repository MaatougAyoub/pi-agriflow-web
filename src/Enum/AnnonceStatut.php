<?php

declare(strict_types=1);

namespace App\Enum;

enum AnnonceStatut: string
{
    case DISPONIBLE = 'DISPONIBLE';
    case RESERVEE = 'RESERVEE';
    case LOUEE = 'LOUEE';
    case VENDUE = 'VENDUE';
    case EXPIREE = 'EXPIREE';

    public function label(): string
    {
        return match ($this) {
            self::DISPONIBLE => 'Disponible',
            self::RESERVEE => 'Reservee',
            self::LOUEE => 'Louee',
            self::VENDUE => 'Vendue',
            self::EXPIREE => 'Expiree',
        };
    }
}
