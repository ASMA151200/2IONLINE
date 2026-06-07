<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Formateur extends Model
{
    /** @use HasFactory<\Database\Factories\FormateurFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialite'
    ];


    //formateur appartient a un user
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // formateur enseigne plusieurs modules
    public function modules():BelongsToMany
    {
        return $this->belongsToMany(Module::class);
    }
}
