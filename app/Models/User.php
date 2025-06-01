<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** @use HasFactory<\Database\Factories\UserFactory> */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        "matricule",
        "role"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'image',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->matricule = self::generateMatricule();
        });
    }
    
    /*private static function generateMatricule()
    {
        $prefix = 'AG';
        $randomNumber = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4)); // 4 caractères aléatoires
        return $prefix . $randomNumber;
    }*/

    private static function generateMatricule()
{
    return str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
}

    public function isGerant()
    {
        return $this->role === 'gerant';
    }

    public function isSuperviseur()
    {
        return $this->role === 'superviseur';
    }

    public function isVendeur()
    {
        return $this->role === 'vendeur';
    }

    public function isActive()
    {
        return $this->status === 1;
    }
        public function getAuthIdentifierName()
    {
        return 'matricule';
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class); // Assurez-vous que 'vendeur_id' est le bon nom de colonne
    }
}
