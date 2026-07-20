<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $table = 'exercice_questions';

    protected $fillable = [
        'exercice_id',
        'contenu',
        'type',
        'points',
        'ordre'
    ];

    public function exercice(): BelongsTo
    {
        return $this->belongsTo(Exercice::class);
    }

    public function choix(): HasMany
    {
        return $this->hasMany(Choix::class)->orderBy('ordre');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(Reponse::class);
    }
}
