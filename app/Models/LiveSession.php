<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'youtube_video_id',
        'scheduled_at',
        'duration_minutes',
        'status',
        'formation_id',
        'user_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
