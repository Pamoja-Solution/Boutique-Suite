<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'total','matricule','user_id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($vente) {
            $vente->matricule = self::generateMatricule();
        });
    }
    public function details()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id'); 
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
    public function detailsVentes()
    {
        return $this->hasMany(DetailVente::class);
    }

    private static function generateMatricule()
    {
        $date = date('Ymd'); // Date au format YYYYMMDD
        $randomString = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6)); // 6 caractères aléatoires
    
        return $date . '-' . $randomString;
    }

    public function scopeForCurrentUser(Builder $query): void
    {
        $query->where('user_id', Auth::user()->id);
    }

    /**
     * Scope pour filtrer les ventes du jour
     */
    public function scopeToday(Builder $query): void
    {
        $query->whereDate('created_at', now()->toDateString());
    }
}
