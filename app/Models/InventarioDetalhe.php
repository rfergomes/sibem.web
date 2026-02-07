<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioDetalhe extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'inventario_detalhes';

    protected $fillable = [
        'inventario_id',
        'id_bem',
        'status_leitura', // encontrado, nao_encontrado, novo_sistema
        'tratativa', // cadastrar, imprimir, alterar, transferir, excluir, nenhuma
        'observacao',
        'id_dependencia_original',
        'user_id_conferencia',
        'timestamp_leitura',
        'is_doacao',
        'documento_doacao_path',
        'documentos_gerados'
    ];

    protected $casts = [
        'is_doacao' => 'boolean',
        'documentos_gerados' => 'array'
    ];

    public function bem()
    {
        return $this->belongsTo(Bem::class, 'id_bem', 'id_bem');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_conferencia');
    }
}
