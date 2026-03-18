<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'local_id',
        'type',
        'title',
        'message',
        'link',
        'related_id',
        'related_type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com o usuário destinatário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o local (opcional)
     */
    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    /**
     * Relacionamento polimórfico com o recurso relacionado
     */
    public function related()
    {
        return $this->morphTo('related');
    }

    /**
     * Scope para notificações não lidas
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope para notificações lidas
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope para notificações de um usuário específico
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para notificações de um local específico
     */
    public function scopeForLocal($query, $localId)
    {
        return $query->where('local_id', $localId);
    }

    /**
     * Scope para notificações de um tipo específico
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Marcar notificação como lida
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Marcar notificação como não lida
     */
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Verificar se a notificação foi lida
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Verificar se a notificação não foi lida
     */
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    /**
     * Obter tempo relativo desde a criação
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
