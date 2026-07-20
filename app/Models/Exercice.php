<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Exercice extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecon_id',
        'titre',
        'description',
        'type',
        'duree',
        'note_max'
    ];

    public function lecon(): BelongsTo
    {
        return $this->belongsTo(Lecon::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('ordre');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(Reponse::class);
    }
}
