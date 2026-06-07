<?php

namespace App\Enums;

enum UserRole:string
{
    case admin = 'admin';
    case etudiant = 'etudiant';
    case user = 'user';
    case formateur ='formateur';
}
