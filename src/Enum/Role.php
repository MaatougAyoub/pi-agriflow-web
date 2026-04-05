<?php

namespace App\Enum;

enum Role: string
{
    case ADMIN = 'ROLE_ADMIN';
    case AGRICULTEUR = 'ROLE_AGRICULTEUR';
    case EXPERT = 'ROLE_EXPERT';
}