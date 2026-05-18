<?php

// app/Models/Progression.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progression extends Model
{
    use HasFactory;

    protected $fillable = [
        'statut',
        'user_id',
        'lecon_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lecon(): BelongsTo
    {
        return $this->belongsTo(Lecon::class);
    }
}
