<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'admlcs_v2';

    protected $fillable = [
        'admlc_id',
        'adm_local',
        'razao_social',
        'cnpj',
        'cidade',
        'uf',
        'status_id',
        'admrg_id'
    ];

    // Accessors for backward compatibility
    public function getNomeAttribute()
    {
        return $this->adm_local;
    }

    public function setNomeAttribute($value)
    {
        $this->attributes['adm_local'] = $value;
    }

    public function getRegionalIdAttribute()
    {
        return $this->admrg_id;
    }

    public function setRegionalIdAttribute($value)
    {
        $this->attributes['admrg_id'] = $value;
    }

    public function regional()
    {
        return $this->belongsTo(Regional::class, 'admrg_id', 'admrg_id');
    }

    public function igrejas()
    {
        return $this->hasMany(Igreja::class, 'admlc_id', 'admlc_id');
    }

    public function setores()
    {
        return $this->hasMany(Setor::class, 'admlc_id', 'admlc_id');
    }

    public function servidor()
    {
        return $this->hasOne(Servidor::class, 'admlc_id', 'admlc_id');
    }
}
