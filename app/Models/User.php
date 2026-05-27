<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'mysql_sys';
    protected $table = 'users_v2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'telefone',
        'email',
        'password',
        'senha_salt',
        'igreja',
        'cidade',
        'tipo',
        'admlc_id',
        'foto',
        'token',
        'remember_token'
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
    ];

    // Relations
    public function local()
    {
        return $this->belongsTo(Local::class, 'admlc_id', 'admlc_id');
    }

    // Accessors and Mutators for backward compatibility
    public function getNomeAttribute()
    {
        return $this->name;
    }

    public function setNomeAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function getLocalIdAttribute()
    {
        return $this->admlc_id;
    }

    public function setLocalIdAttribute($value)
    {
        $this->attributes['admlc_id'] = $value;
    }

    public function getRegionalIdAttribute()
    {
        return $this->local ? $this->local->admrg_id : null;
    }

    public function getPerfilAttribute()
    {
        return $this->tipo;
    }

    // Role-Checking Helpers using 'tipo'
    public function isAdminSistema()
    {
        return $this->tipo === 'admin_sistema';
    }

    public function isAdminRegional()
    {
        return $this->tipo === 'admin_regional';
    }

    public function isAdminLocal()
    {
        return $this->tipo === 'admin_local';
    }

    public function isOperador()
    {
        return $this->tipo === 'operador';
    }

    public function isAuditor()
    {
        return $this->tipo === 'auditor';
    }

    public function getAvailableLocais()
    {
        if ($this->isAdminSistema()) {
            return Local::orderBy('adm_local')->get();
        } elseif ($this->isAdminRegional()) {
            return Local::where('admrg_id', $this->regional_id)->orderBy('adm_local')->get();
        } else {
            return Local::where('admlc_id', $this->admlc_id)->get();
        }
    }

    /**
     * Get the password for the user.
     * Recombines the salt and password hash so that the custom PBKDF2 hasher can verify it.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        if (!empty($this->senha_salt)) {
            return "pbkdf2:{$this->senha_salt}:{$this->password}";
        }
        return $this->password;
    }

    /**
     * Set the password attribute.
     * Intercepts PBKDF2 strings and splits them into password and senha_salt.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        if (str_starts_with($value, 'pbkdf2:')) {
            $parts = explode(':', $value, 3);
            $this->attributes['senha_salt'] = $parts[1];
            $this->attributes['password'] = $parts[2];
        } else {
            $this->attributes['password'] = $value;
            $this->attributes['senha_salt'] = null;
        }
    }
}
