<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mentorat extends Model
{
    protected $fillable = ['mentor_id', 'mentore_id', 'statut', 'message_demande'];

    public function mentor(): BelongsTo { return $this->belongsTo(User::class, 'mentor_id'); }
    public function mentore(): BelongsTo { return $this->belongsTo(User::class, 'mentore_id'); }
}
