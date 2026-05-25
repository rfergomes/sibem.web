<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bem extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'bens_v2';

    protected $fillable = [
        'bem_id',
        'descricao',
        'igreja_id',
        'dependencia_id',
        'status_id',
        'tipo_id'
    ];

    public function igreja()
    {
        return $this->belongsTo(Igreja::class, 'igreja_id', 'codigo_ccb');
    }
}
