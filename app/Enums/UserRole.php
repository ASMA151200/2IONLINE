<?php

namespace App\Enums;

enum UserRole:string
{
    case ADMIN = 'admin';
    case etudiant = 'etudiant';
    case user = 'user';
    case formateur ='formateur';
}
