<?php

// app/Models/Actu.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actu extends Model
{
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
// Optionnel : si tu veux un auteur :
// php
// public function user(): BelongsTo
// {
    // return $this->belongsTo(User::class, 'user_id');
// }
