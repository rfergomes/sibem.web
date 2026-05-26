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
        'setor_id',
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
}
