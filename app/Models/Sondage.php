<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sondage extends Model
{
    protected $fillable = ['formation_id', 'titre', 'questions'];
    protected $casts = ['questions' => 'array'];

    public function formation(): BelongsTo { return $this->belongsTo(Formation::class); }
    public function reponses(): HasMany { return $this->hasMany(SondageReponse::class); }
}
