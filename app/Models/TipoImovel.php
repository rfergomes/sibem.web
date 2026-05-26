<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoImovel extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'tipos_imovel';

    protected $fillable = [
        'nome'
    ];

    public function igrejas()
    {
        return $this->hasMany(Igreja::class, 'tipo_id', 'id');
    }
}
