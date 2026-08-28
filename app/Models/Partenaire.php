<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Partenaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom_organisation',
        'secteur',
    ];

    // partenaire appartient a un user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // partenaire finance plusieurs formations (avec montant/date sur le pivot)
    public function formations(): BelongsToMany
    {
        return $this->belongsToMany(Formation::class, 'formation_partenaire')
            ->withPivot(['montant_finance', 'date_financement'])
            ->withTimestamps();
    }
}
