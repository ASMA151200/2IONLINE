<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SondageReponse extends Model
{
    protected $table = 'sondage_reponses';
    protected $fillable = ['sondage_id', 'user_id', 'reponses'];
    protected $casts = ['reponses' => 'array'];

    public function sondage(): BelongsTo { return $this->belongsTo(Sondage::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
