<?php

namespace App\Enum;

enum TypeTerre: string
{
    case ARGILEUSE = 'ARGILEUSE';
    case SABLEUSE = 'SABLEUSE';
    case LIMONEUSE = 'LIMONEUSE';
    case CALCAIRE = 'CALCAIRE';
    case HUMIFERE = 'HUMIFERE';
    case SALINE = 'SALINE';
    case MIXTE = 'MIXTE';
    case AUTRE = 'AUTRE';
}
