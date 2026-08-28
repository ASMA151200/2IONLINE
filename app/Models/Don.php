<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Don extends Model
{
    protected $fillable = ['user_id', 'nom_donateur', 'email_donateur', 'montant', 'message', 'paydunya_token', 'statut'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
