<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sys';
    protected $table = 'agendamentos_v2';

    protected $fillable = [
        'admlc_id',
        'igreja_id',
        'user_id',
        'responsavel_nome',
        'responsavel_telefone',
        'acompanhante_nome',
        'data',
        'horario',
        'status',
        'motivo_cancelamento',
        'observacao'
    ];

    /**
     * Get the church associated with this schedule.
     */
    public function igreja()
    {
        return $this->belongsTo(Igreja::class, 'igreja_id', 'id');
    }

    /**
     * Get the local administration associated with this schedule.
     */
    public function local()
    {
        return $this->belongsTo(Local::class, 'admlc_id', 'admlc_id');
    }

    /**
     * Get the user (operator) who registered this schedule.
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
