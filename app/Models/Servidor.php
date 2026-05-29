<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servidor extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'servidores_v2';

    protected $fillable = [
        'admlc_id',
        'descricao',
        'servidor',
        'porta',
        'banco',
        'usuario',
        'senha',
        'ativo',
        'provisionado',
        'data_provisionamento'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'provisionado' => 'boolean',
        'data_provisionamento' => 'datetime'
    ];

    public function local()
    {
        return $this->belongsTo(Local::class, 'admlc_id', 'admlc_id');
    }
}
