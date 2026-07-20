<?php

namespace App\Models;

<<<<<<< HEAD
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
=======

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
>>>>>>> gestion_lecon

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
<<<<<<< HEAD
    use HasFactory, Notifiable;
=======
    use HasApiTokens, HasFactory, Notifiable;
>>>>>>> gestion_lecon

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
<<<<<<< HEAD
        'name',
        'email',
        'password',
=======
        'prenom',
        'nom',
        'telephone',
        'email',
        'password',
        'photo',
        'role'
>>>>>>> gestion_lecon
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
<<<<<<< HEAD
=======


    // Relations
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function progressions(): HasMany
    {
        return $this->hasMany(Progression::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function certificats(): HasMany
    {
        return $this->hasMany(Certificat::class);
    }

    public function resultats(): HasMany
    {
        return $this->hasMany(Resultat::class);
    }

    public function formations(): HasMany
    {
        return $this->hasMany(Formation::class, 'user_id');
    }

    public function formateur(): HasOne
    {
        return $this->hasOne(Formateur::class);
    }

    public function etudiant(): HasOne
    {
        return $this->hasOne(Etudiant::class);
    }

>>>>>>> gestion_lecon
}
