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
        'module_id',
    ];

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
