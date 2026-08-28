<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = ['code', 'titre', 'description', 'icone', 'points'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')->withPivot('obtenu_le');
    }
}
