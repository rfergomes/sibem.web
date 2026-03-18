<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Igreja;
use App\Models\Local;

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
        'inventariante',
        'documentos_gerados'
    ];

    protected $casts = [
        'is_sincronizado' => 'boolean',
        'documentos_gerados' => 'array'
    ];

    public function detalhes()
    {
        return $this->hasMany(InventarioDetalhe::class, 'inventario_id');
    }

    public function igreja()
    {
        return $this->belongsTo(Igreja::class, 'id_igreja');
    }

    public function getHeaderDataAttribute()
    {
        $igreja = $this->igreja;
        $local = $igreja ? $igreja->local : null;

        return [
            'administracao' => $local ? $local->nome : 'Administração Desconhecida',
            'codigo_ccb' => $igreja ? $igreja->codigo_ccb : 'N/A',
            'cod_siga' => $igreja ? $igreja->cod_siga : 'N/A',
            'razao_social' => $igreja->razao_social ?? $local->razao_social ?? 'RAZÃO SOCIAL NÃO CADASTRADA',
            'cnpj' => $igreja->cnpj ?? $local->cnpj ?? 'CNPJ NÃO CADASTRADO',
            'cidade' => $igreja->cidade ?? $local->cidade ?? 'Cidade N/A',
            'uf' => $igreja->uf ?? $local->uf ?? 'UF',
            'logradouro' => $igreja->logradouro ?? 'Logradouro N/A',
            'numero' => $igreja->numero ?? 'S/N',
            'setor' => $igreja->setor ?? 'Setor N/A'
        ];
    }
}
