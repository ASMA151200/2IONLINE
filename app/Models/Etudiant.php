<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;



class Etudiant extends Model
{
    protected $fillable = [
        'user_id',
        'date_naissance',
        'lieu_naissance',
        'niveau'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formations(): BelongsToMany
    {
        return $this->belongsToMany(Formation::class, 'etudiant_formation')
                    ->withTimestamps();
    }
}
