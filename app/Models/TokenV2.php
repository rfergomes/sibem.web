<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenV2 extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'tokens_v2';

    protected $fillable = [
        'token',
        'dispositivo',
        'admlc_id',
        'user_id',
        'ativo'
    ];

    // Accessors for backward compatibility
    public function getIdentificadorMaquinaAttribute()
    {
        return $this->dispositivo;
    }

    public function setIdentificadorMaquinaAttribute($value)
    {
        $this->attributes['dispositivo'] = $value;
    }

    public function getLocalIdAttribute()
    {
        return $this->admlc_id;
    }

    public function setLocalIdAttribute($value)
    {
        $this->attributes['admlc_id'] = $value;
    }

    public function getActiveAttribute()
    {
        return (bool)$this->ativo;
    }

    public function setActiveAttribute($value)
    {
        $this->attributes['ativo'] = $value ? 1 : 0;
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'admlc_id', 'admlc_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIsPendingAttribute()
    {
        return !$this->ativo || $this->admlc_id == 0;
    }
}
