<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'setores_v2';

    protected $fillable = [
        'cod_setor',
        'descricao',
        'admlc_id'
    ];

    // Accessors for backward compatibility
    public function getNomeAttribute()
    {
        return $this->descricao;
    }

    public function setNomeAttribute($value)
    {
        $this->attributes['descricao'] = $value;
    }

    public function getActiveAttribute()
    {
        return true;
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'admlc_id', 'admlc_id');
    }

    public function igrejas()
    {
        return $this->hasMany(Igreja::class, 'cod_setor', 'cod_setor')
            ->whereColumn('igrejas_v2.admlc_id', '=', 'setores_v2.admlc_id');
    }
}
