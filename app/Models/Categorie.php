<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    /** @use HasFactory<\Database\Factories\CategorieFactory> */
    use HasFactory;

     protected $fillable = [
        'titre',
        'description',
    ];


    // Relation
     public function formations(): HasMany
    {
        return $this->hasMany(Formation::class);
    }

}
