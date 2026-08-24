<?php
// app/Models/Alerte.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerte extends Model
{
    protected $fillable = [
        'formation_id', 'formateur_id', 'live_session_id',
        'type', 'titre', 'message', 'envoye_le', 'nb_push_envoyes',
    ];

    protected $casts = ['envoye_le' => 'datetime'];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function formateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'formateur_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }
}