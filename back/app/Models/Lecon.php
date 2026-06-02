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
