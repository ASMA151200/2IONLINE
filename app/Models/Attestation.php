<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attestation extends Model
{
    protected $fillable = ['numero_attestation', 'fichier_pdf', 'user_id', 'live_session_id', 'date_delivrance'];
    protected $casts = ['date_delivrance' => 'date'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function liveSession(): BelongsTo { return $this->belongsTo(LiveSession::class); }
}
