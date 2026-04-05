<?php

namespace App\Enum;

enum Categorie: string
{
    case TECHNIQUE = 'CATEGORIE_TECHNIQUE';
    case ACCESS = 'CATEGORIE_ACCESS';
    case DELIVERY = 'CATEGORIE_DELIVERY';
    case PAIMENT = 'CATEGORIE_PAIMENT';
    case SERVICE = 'CATEGORIE_SERVICE'; 
    case AUTRE = 'CATEGORIE_AUTRE'; 

}