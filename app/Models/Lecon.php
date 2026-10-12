<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lecon extends Model
{
    /** @use HasFactory<\Database\Factories\LeconFactory> */
    use HasFactory;

    protected $fillable = [
        'titre',
        'contenu',
        'video',
        'document',
        'ordre',
        'publie',
        'module_id',
    ];

    protected $casts = [
        'publie' => 'boolean',
    ];

    /**
     * Un apprenant ne voit que les leçons PUBLIÉES. Le scope est global :
     * il couvre automatiquement l'API des leçons, les modules chargés avec
     * leurs leçons, "mes cours", la recherche et les exercices rattachés,
     * sans modifier chaque contrôleur. Formateur/admin voient tout (ainsi
     * que les traitements sans utilisateur connecté : seeders, jobs).
     */
    protected static function booted(): void
    {
        static::addGlobalScope('publiees_pour_apprenants', function ($builder) {
            $user = \Illuminate\Support\Facades\Auth::user();

            if ($user && $user->role === 'etudiant') {
                $builder->where('lecons.publie', true);
            }
        });
    }

    /**
     * Transforme le chemin relatif de stockage en URL complète et
     * accessible — mais laisse un lien externe (YouTube, Vimeo, ou tout
     * lien direct .mp4 saisi par le professeur) parfaitement intact, sans
     * jamais le préfixer par le domaine de stockage (ce qui produirait
     * une URL absurde du type ".../storage/https://youtube.com/...").
     */
    public function getVideoAttribute(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    }

    public function getDocumentAttribute(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    }


    //Relation
     public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function progressions(): HasMany
    {
        return $this->hasMany(Progression::class);
    }


}
