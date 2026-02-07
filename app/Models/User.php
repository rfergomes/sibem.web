<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
        'perfil_id',
        'regional_id',
        'local_id',
        'active',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    // New Multi-Local Relation
    public function locais()
    {
        return $this->belongsToMany(Local::class, 'local_user');
    }

    /**
     * Get all locals the user is authorized to access based on their profile.
     */
    public function getAuthorizedLocaisAttribute()
    {
        // Perfil 1: Admin Sistema can see ALL active locals
        if ($this->perfil_id == 1) {
            return Local::where('active', true)->get();
        }

        // Perfil 2: Admin Regional can see all active locals in their regional
        if ($this->perfil_id == 2 && $this->regional_id) {
            return Local::where('regional_id', $this->regional_id)
                ->where('active', true)
                ->get();
        }

        // Others: Only locals linked in the pivot table
        return $this->locais()->where('active', true)->get();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
