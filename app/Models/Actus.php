<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actus extends Model
{
    /** @use HasFactory<\Database\Factories\ActusFactory> */
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'contenu_html',
         'image',
        'type',
        'date_publication',
        'date_expiration',
        'statut',
    ];

    /**
     * Transforme le chemin relatif de stockage en URL complète et
     * accessible — même bug que celui trouvé et corrigé sur Lecon
     * (video/document) : le chemin brut était renvoyé tel quel, jamais
     * utilisable directement par le frontend.
     */
    public function getImageAttribute(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    }

}
