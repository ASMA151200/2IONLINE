<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PartenaireLogo extends Model
{
    protected $fillable = ['nom', 'domaine', 'logo', 'ordre', 'actif'];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
    ];

    /** URL complète du logo (voir Actus::getImageAttribute). */
    public function getLogoAttribute(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }
}
