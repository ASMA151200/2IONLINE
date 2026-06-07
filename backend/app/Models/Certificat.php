<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificat extends Model
{
    /** @use HasFactory<\Database\Factories\CertificatFactory> */
    use HasFactory;

     protected $fillable = [
        'numero_certificat',
        'fichier_pdf',
        'date_obtention',
        'date_expiration',
        'user_id',
        'formation_id',
    ];


    //Relation
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }


}
