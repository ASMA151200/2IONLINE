<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reponse extends Model
{
    /** @use HasFactory<\Database\Factories\ReponseFactory> */
    use HasFactory;
    protected $fillable = [
        'texte',
        'est_correct',
        'ordre',
        'question_id',
    ];


    //Relations
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

}
