<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    protected $fillable = ['live_session_id', 'user_id', 'present', 'marked_at'];
    protected $casts = ['present' => 'boolean', 'marked_at' => 'datetime'];

    public function liveSession(): BelongsTo { return $this->belongsTo(LiveSession::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
