<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Igreja extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'igrejas_v2';

    protected $fillable = [
        'igreja_id',
        'igreja',
        'cod_siga',
        'razao_social',
        'cnpj',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'uf',
        'tipo_id',
        'status_id',
        'cod_setor',
        'admlc_id',
        'observacao'
    ];

    // Accessors for backward compatibility
    public function getNomeAttribute()
    {
        return $this->igreja;
    }

    public function setNomeAttribute($value)
    {
        $this->attributes['igreja'] = $value;
    }

    public function getLocalIdAttribute()
    {
        return $this->admlc_id;
    }

    public function setLocalIdAttribute($value)
    {
        $this->attributes['admlc_id'] = $value;
    }

    public function getSetorAttribute()
    {
        return $this->cod_setor;
    }

    public function setSetorAttribute($value)
    {
        $this->attributes['cod_setor'] = $value;
    }

    public function getCodigoCcbAttribute()
    {
        return $this->igreja_id;
    }

    public function setCodigoCcbAttribute($value)
    {
        $this->attributes['igreja_id'] = $value;
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'admlc_id', 'admlc_id');
    }
}
