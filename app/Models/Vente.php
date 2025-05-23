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
        $date = date('Ymd'); // Format: YYYYMMDD
        $lastRecord = Vente::whereDate('created_at', today())->latest()->first();
        
        $counter = 1;
        if ($lastRecord && preg_match('/\d{5}$/', $lastRecord->matricule)) {
            $lastCounter = (int)substr($lastRecord->matricule, -5);
            $counter = $lastCounter + 1;
        }
        
        return $date . str_pad($counter, 5, '0', STR_PAD_LEFT);
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
