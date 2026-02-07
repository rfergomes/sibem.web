<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventario extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'inventarios';

    protected $fillable = [
        'ano',
        'mes',
        'codigo_unico',
        'id_igreja',
        'status', // aberto, fechado, auditado
        'is_sincronizado',
        'user_id_abertura',
        'responsavel',
        'inventariante'
    ];

    public function detalhes()
    {
        return $this->hasMany(InventarioDetalhe::class, 'inventario_id');
    }

    // Helper to get global church name
    public function getIgrejaNomeAttribute()
    {
        // Must dynamically query global DB
        // NOTE: Ideally we replicate basic church info to tenant or cache it.
        // For now, simple query to global:
        $globalChurch = DB::connection('mysql')
            ->table('igrejas_global')
            ->where('id', $this->id_igreja) // Assuming id_igreja maps to global ID
            ->first();

        return $globalChurch ? $globalChurch->nome : "Igreja #{$this->id_igreja}";
    }
}
