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
        'image',
        'date_debut',
        'date_fin',
        'ville',
        'pays',
        'entreprise',
        'lien_inscription',
        'statut',
    ];

    /**
     * Transforme le chemin relatif de stockage en URL complète —
     * documents n'avait jamais eu cet accesseur non plus (même bug
     * trouvé et corrigé sur Lecon et Actus), corrigé au passage.
     */
    public function getImageAttribute(?string $value): ?string
    {
        return $this->toStorageUrl($value);
    }

    public function getDocumentsAttribute(?string $value): ?string
    {
        return $this->toStorageUrl($value);
    }

    private function toStorageUrl(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    }


    //Relations
    public function candidatures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Candidature::class);
    }


}
