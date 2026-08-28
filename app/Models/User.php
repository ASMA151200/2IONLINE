<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [

        'prenom',
        'nom',
        'telephone',
        'email',
        'password',
        'photo',
        'role',
        'is_active'
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
            'is_active' => 'boolean',
        ];
    }


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

    public function partenaire(): HasOne
    {
        return $this->hasOne(Partenaire::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withPivot('obtenu_le');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function mentoratsAsMentor(): HasMany
    {
        return $this->hasMany(Mentorat::class, 'mentor_id');
    }

    public function mentoratsAsMentore(): HasMany
    {
        return $this->hasMany(Mentorat::class, 'mentore_id');
    }



}
