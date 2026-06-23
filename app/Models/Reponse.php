<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reponse extends Model
{
    use HasFactory;

    protected $table = 'exercice_reponses';

    protected $fillable = [
        'exercice_id',
        'user_id',
        'question_id',
        'choix_id',
        'reponse_texte',
        'score',
        'statut',
        'commentaire_formateur'
    ];

    public function exercice(): BelongsTo
    {
        return $this->belongsTo(Exercice::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function choix(): BelongsTo
    {
        return $this->belongsTo(Choix::class);
    }

}
