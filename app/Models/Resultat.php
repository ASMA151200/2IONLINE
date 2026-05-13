<?php

// app/Models/Resultat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resultat extends Model
{
    use HasFactory;

    protected $fillable = [
        'score',
        'date_passage',
        'statut',
        'user_id',
        'examen_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }
}
