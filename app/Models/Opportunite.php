<?php

// app/Models/Opportunite.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opportunite extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'type',
        'description',
        'documents',
        'date_debut',
        'date_fin',
        'ville',
        'pays',
        'entreprise',
        'lien_inscription',
        'statut',
        'formation_id',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }
}
