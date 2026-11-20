<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscription extends Model
{
    /** @use HasFactory<\Database\Factories\InscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'statut',
        'user_id',
        'formation_id',
    ];


    //Relation
     public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }



    /**
     * RÈGLE MÉTIER : un apprenant ne suit qu'UNE SEULE formation à la fois
     * (une seule inscription au statut « actif »). Renvoie l'inscription active
     * qui entre en conflit (autre formation, autre inscription), ou null.
     */
    public static function conflitFormationActive(int $userId, int $formationId, ?int $exceptInscriptionId = null): ?self
    {
        return static::where('user_id', $userId)
            ->where('statut', 'actif')
            ->where('formation_id', '!=', $formationId)
            ->when($exceptInscriptionId, fn ($q) => $q->where('id', '!=', $exceptInscriptionId))
            ->with('formation:id,titre')
            ->first();
    }

    public static function messageUneSeuleFormation(self $conflit): string
    {
        $titre = $conflit->formation->titre ?? 'une autre formation';

        return "Cet apprenant suit déjà la formation « {$titre} ». "
            . 'Un apprenant ne peut suivre qu\'une seule formation à la fois.';
    }
}
