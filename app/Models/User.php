<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'avatar',
        'password',
        'perfil_id',
        'regional_id',
        'local_id',
        'default_local_id',
        'active',
        'must_change_password',
        'notification_settings',
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
        'notification_settings' => 'array',
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

    /**
     * Get the avatar URL or null if not set.
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    /**
     * Get user initials for avatar fallback.
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', trim($this->nome));
        if (count($words) >= 2) {
            return strtoupper($words[0][0] . $words[1][0]);
        }
        return strtoupper(substr($this->nome, 0, 2));
    }
}
