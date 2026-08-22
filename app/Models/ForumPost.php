<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ForumPost extends Model
{
    protected $fillable = ['formation_id', 'user_id', 'title', 'content', 'is_pinned'];

    protected $casts = ['is_pinned' => 'boolean'];

    protected $appends = ['reply_count', 'like_count'];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'post_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    public function getReplyCountAttribute(): int
    {
        return $this->replies()->count();
    }

    public function getLikeCountAttribute(): int
    {
        return $this->likes()->count();
    }
}
