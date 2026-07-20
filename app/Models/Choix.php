<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Choix extends Model
{
    use HasFactory;

    protected $table = 'choix';

    protected $fillable = [
        'question_id',
        'contenu',
        'est_correct',
        'ordre'
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
