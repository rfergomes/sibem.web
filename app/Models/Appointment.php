<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'igreja_id',
        'local_id',
        'user_id',
        'responsavel_nome',
        'responsavel_cargo',
        'responsavel_contato',
        'scheduled_at',
        'status',
        'notes',
        'justification',
        'action_user_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Localidade do agendamento
     */
    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    /**
     * Igreja do agendamento (opcional, se for direto na ADM fica null)
     */
    public function igreja()
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    /**
     * Criador do agendamento
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuário que realizou a última ação (auditoria)
     */
    public function actionUser()
    {
        return $this->belongsTo(User::class, 'action_user_id');
    }

    /**
     * Verifica se o agendamento está confirmado
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmado';
    }

    /**
     * Verifica se o agendamento está cancelado
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelado';
    }
}
