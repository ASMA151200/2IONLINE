<?php

// app/Models/Question.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'texte',
        'type',
        'points',
        'ordre',
        'examen_id',
    ];

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(Reponse::class);
    }
}
