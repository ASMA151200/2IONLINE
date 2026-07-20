<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actus extends Model
{
    /** @use HasFactory<\Database\Factories\ActusFactory> */
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'contenu_html',
         'image',
        'type',
        'date_publication',
        'date_expiration',
        'statut',
    ];


}
