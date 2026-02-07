<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divergencia extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'divergencias_inventario';

    protected $fillable = [
        'inventario_id',
        'id_bem',
        'codigo_divergencia',
        'descricao',
        'id_dependencia_anterior',
        'id_dependencia_nova',
        'registrado_por'
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function bem()
    {
        return $this->belongsTo(Bem::class, 'id_bem', 'id_bem');
    }

    public function dependenciaNova()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_nova');
    }
}
