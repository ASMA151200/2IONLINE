<?php

// app/Models/Examen.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examen extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'titre',
        'description',
        'duree_minutes',
        'bareme_pts',
        'formation_id',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function resultats(): HasMany
    {
        return $this->hasMany(Resultat::class);
    }
}
