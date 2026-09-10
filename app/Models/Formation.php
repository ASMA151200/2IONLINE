<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Formation extends Model
{
    /** @use HasFactory<\Database\Factories\FormationFactory> */
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'image',
        'user_id',
        'niveau',
        'duree',
        'prix',
        'statut',
        'nb_inscrit',
        'categorie_id',
    ];


    // Relation
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Formateurs pouvant intervenir dans cette formation (créer/modifier
     * modules, leçons, évaluations, sessions live...) — plusieurs-à-
     * plusieurs, contrairement à "user_id" qui ne représente qu'un
     * propriétaire principal à titre informatif. C'est CETTE relation
     * qui fait foi pour le contrôle d'accès (voir ChecksFormationOwnership).
     */
    public function formateurs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'formation_formateur', 'formation_id', 'user_id')
            ->withTimestamps();
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function certificats(): HasMany
    {
        return $this->hasMany(Certificat::class);
    }

    public function examens(): HasMany
    {
        return $this->hasMany(Examen::class);
    }

    public function opportunites(): HasMany
    {
        return $this->hasMany(Opportunite::class);
    }

    public function partenaires(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Partenaire::class, 'formation_partenaire')
            ->withPivot(['montant_finance', 'date_financement'])
            ->withTimestamps();
    }

    public function sondages(): HasMany
    {
        return $this->hasMany(Sondage::class);
    }

}
