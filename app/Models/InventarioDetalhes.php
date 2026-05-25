<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioDetalhes extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'inventario_detalhes_v2';

    protected $fillable = [
        'inventario_id',
        'bem_id',
        'situacao',
        'acao',
        'cad_desc',
        'dependencia_id',
        'observacao',
        'cont'
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id', 'inventario_id');
    }

    public function bem()
    {
        return $this->belongsTo(Bem::class, 'bem_id', 'bem_id');
    }
}
