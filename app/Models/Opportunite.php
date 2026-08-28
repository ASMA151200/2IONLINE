<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Candidature;

class Opportunite extends Model
{
    /** @use HasFactory<\Database\Factories\OpportuniteFactory> */
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
    ];


    //Relations
    public function candidatures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Candidature::class);
    }


}
