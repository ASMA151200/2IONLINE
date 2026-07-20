<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPasswordCode extends Model
{
    protected $fillable = [
        'email',
        'code',
        'expire_at'
    ];

    protected $casts = [
        'expire_at' => 'datetime',
    ];

    // Vérifier si le code est expiré
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expire_at);
    }
}
