<?php
// app/Models/Alerte.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    protected $fillable = [
        'cours_id', 'formateur_id', 'live_session_id',
        'type', 'titre', 'message', 'envoye_le', 'nb_push_envoyes',
    ];

    protected $casts = ['envoye_le' => 'datetime'];

    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }

    public function formateur()
    {
        return $this->belongsTo(User::class, 'formateur_id');
    }
}