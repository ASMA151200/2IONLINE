<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleFactory> */
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'ordre',
        'formation_id',
    ];


    //Relation
     public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function lecons(): HasMany
    {
        return $this->hasMany(Lecon::class);
    }


}
