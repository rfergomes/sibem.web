<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Local;
use App\Models\SolicitacaoAcesso;
use App\Models\Inventario;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Criar notificação para nova solicitação de acesso
     */
    public function createAccessRequestNotification(SolicitacaoAcesso $accessRequest)
    {
        // Determinar destinatários baseado no perfil
        $recipients = $this->getAccessRequestRecipients($accessRequest);

        foreach ($recipients as $user) {
            if (!$this->shouldNotify($user, 'access_request'))
                continue;

            Notification::create([
                'user_id' => $user->id,
                'local_id' => null, // Solicitações de acesso são globais
                'type' => 'access_request',
                'title' => 'Nova Solicitação de Acesso',
                'message' => "{$accessRequest->nome} solicitou acesso ao sistema",
                'link' => '/admin/solicitacoes',
                'related_id' => $accessRequest->id,
                'related_type' => SolicitacaoAcesso::class,
            ]);

            // Real-time Push
            $this->sendPushNotification($user, 'access_request', 'Nova Solicitação de Acesso', "{$accessRequest->nome} solicitou acesso ao sistema", '/admin/solicitacoes');
        }
    }

    /**
     * Criar notificação para novo contato via Landing Page
     */
    public function createLandingContactNotification($contact)
    {
        // Admin Sistema (perfil_id = 1) - Sempre recebe
        $recipients = User::where('perfil_id', 1)
            ->where('active', true)
            ->get();

        foreach ($recipients as $user) {
            Notification::create([
                'user_id' => $user->id,
                'local_id' => null,
                'type' => 'landing_contact',
                'title' => 'Contato Landing Page',
                'message' => "Nova mensagem de {$contact->name}: {$contact->subject}",
                'link' => '#', // No link yet
                'related_id' => $contact->id,
                'related_type' => \App\Models\ContactMessage::class,
            ]);

            // Real-time Push (if applicable)
            $this->sendPushNotification($user, 'landing_contact', 'Contato Landing Page', "{$contact->name} enviou uma mensagem", '#');
        }
    }

    /**
     * Criar notificação para status de solicitação de acesso (Aprovada/Rejeitada)
     */
    public function createAccessRequestStatusNotification(SolicitacaoAcesso $accessRequest, string $status)
    {
        $user = User::where('email', $accessRequest->email)->first();

        if (!$user || !$this->shouldNotify($user, 'access_request_status'))
            return;

        $message = $status === 'approved'
            ? "Sua solicitação de acesso foi aprovada! Bem-vindo ao SIBEM."
            : "Sua solicitação de acesso foi indeferida.";

        Notification::create([
            'user_id' => $user->id,
            'local_id' => null,
            'type' => 'access_request_status',
            'title' => 'Status da Solicitação',
            'message' => $message,
            'link' => '/dashboard',
            'related_id' => $accessRequest->id,
            'related_type' => SolicitacaoAcesso::class,
        ]);

        // Real-time Push
        $this->sendPushNotification($user, 'access_request_status', 'Status da Solicitação', $message, '/dashboard');
    }

    /**
     * Criar notificação para inventário em aberto
     */
    public function createInventoryOpenNotification(Inventario $inventory, int $daysOpen)
    {
        // Define local_id if not present but discoverable
        if (!$inventory->local_id && $inventory->igreja) {
            $inventory->local_id = $inventory->igreja->local_id;
        }

        // Obter usuários com acesso ao local do inventário
        $recipients = $this->getInventoryRecipients($inventory);

        foreach ($recipients as $user) {
            if (!$this->shouldNotify($user, 'inventory_open'))
                continue;

            Notification::create([
                'user_id' => $user->id,
                'local_id' => $inventory->local_id,
                'type' => 'inventory_open',
                'title' => 'Inventário em Aberto',
                'message' => "Inventário #{$inventory->id} está aberto há {$daysOpen} dias",
                'link' => '/inventarios',
                'related_id' => $inventory->id,
                'related_type' => Inventario::class,
            ]);

            // Real-time Push
            $this->sendPushNotification($user, 'inventory_open', 'Inventário em Aberto', "Inventário #{$inventory->id} está aberto há {$daysOpen} dias", '/inventarios');
        }
    }

    /**
     * Obter contagem de notificações não lidas para um usuário
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::forUser($userId)
            ->unread()
            ->count();
    }

    /**
     * Obter notificações recentes de um usuário
     */
    public function getRecentNotifications(int $userId, int $limit = 10)
    {
        return Notification::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Marcar notificação como lida
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Marcar todas as notificações de um usuário como lidas
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::forUser($userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Obter destinatários para notificação de solicitação de acesso
     */
    private function getAccessRequestRecipients(SolicitacaoAcesso $accessRequest)
    {
        $recipients = collect();

        // Admin Sistema (perfil_id = 1) - Sempre recebe
        $recipients = $recipients->merge(
            User::where('perfil_id', 1)
                ->where('active', true)
                ->get()
        );

        // Admin Regional (perfil_id = 2) - Recebe se a solicitação for da sua regional
        if ($accessRequest->regional_id) {
            $recipients = $recipients->merge(
                User::where('perfil_id', 2)
                    ->where('regional_id', $accessRequest->regional_id)
                    ->where('active', true)
                    ->get()
            );
        }

        return $recipients->unique('id');
    }

    /**
     * Obter destinatários para notificação de inventário
     */
    private function getInventoryRecipients(Inventario $inventory)
    {
        // Usuários que têm acesso ao local do inventário
        // Isso pode ser ajustado conforme a lógica de permissões do sistema

        $recipients = collect();

        // Admin Sistema sempre recebe
        $recipients = $recipients->merge(
            User::where('perfil_id', 1)
                ->where('active', true)
                ->get()
        );

        // Usuários vinculados ao local específico
        if ($inventory->local_id) {
            $recipients = $recipients->merge(
                User::whereHas('locais', function ($query) use ($inventory) {
                    $query->where('locais.id', $inventory->local_id);
                })
                    ->where('active', true)
                    ->get()
            );
        }

        return $recipients->unique('id');
    }

    /**
     * Deletar notificações antigas (limpeza)
     */
    public function deleteOldNotifications(int $daysOld = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($daysOld))
            ->where('read_at', '!=', null) // Apenas notificações lidas
            ->delete();
    }

    /**
     * Verificar se o usuário deve ser notificado sobre um tipo específico
     */
    private function shouldNotify(User $user, string $type): bool
    {
        $settings = $user->notification_settings;

        if (!$settings) {
            return true; // Default behavior
        }

        return (bool) ($settings[$type] ?? true);
    }

    /**
     * Enviar notificação push se configurado
     */
    private function sendPushNotification(User $user, string $type, string $title, string $body, string $link)
    {
        // Verificar se push está habilitado globalmente para o usuário
        $settings = $user->notification_settings;
        if (!($settings['browser_push'] ?? false)) {
            return;
        }

        // Verificar se este tipo específico de notificação está habilitado
        if (!$this->shouldNotify($user, $type)) {
            return;
        }

        try {
            $user->notify(new GeneralNotification($title, $body, $link));
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar push notification: " . $e->getMessage());
        }
    }
}
