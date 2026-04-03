<?php

namespace App\Enum;

enum EtatCulture: string
{
    case EN_COURS = 'EN_COURS';
    case RECOLTEE = 'RECOLTEE';
    case EN_VENTE = 'EN_VENTE';
    case VENDUE = 'VENDUE';
}
