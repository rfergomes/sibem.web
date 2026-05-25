<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'inventarios_v2';

    protected $fillable = [
        'inventario_id',
        'igreja_id',
        'data',
        'responsaveis',
        'inventariantes',
        'inicio',
        'termino',
        'tempo',
        'situacao',
        'bens_inicial',
        'bens_lidos',
        'bens_pendentes',
        'bens_novos',
        'bens_final',
        'bens_importado',
        'teste',
        'siga_ok',
        'pdf',
        'admlc_id'
    ];

    public function igreja()
    {
        return $this->belongsTo(Igreja::class, 'igreja_id', 'igreja_id');
    }

    // Accessors for backward compatibility
    public function getAnoAttribute()
    {
        return $this->data ? date('Y', strtotime($this->data)) : null;
    }

    public function getMesAttribute()
    {
        return $this->data ? (int)date('m', strtotime($this->data)) : null;
    }

    public function getCodigoUnicoAttribute()
    {
        return $this->inventario_id;
    }

    public function getStatusAttribute()
    {
        return strtolower($this->situacao);
    }
}
