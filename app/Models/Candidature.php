<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidature extends Model
{
    protected $fillable = ['opportunite_id', 'user_id', 'message', 'statut'];

    public function opportunite(): BelongsTo { return $this->belongsTo(Opportunite::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
