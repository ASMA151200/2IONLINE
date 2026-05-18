<?php

namespace App\Enums;

enum UserRole:string
{
    case ADMIN = 'admin';
    case APPRENANT = 'apprenant';
    case FORMATEUR = 'formateur';
}
