<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Obter notificações do usuário autenticado
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $notifications = $this->notificationService->getRecentNotifications(
            auth()->id(),
            $limit
        );

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'time_ago' => $notification->time_ago,
                    'is_read' => $notification->isRead(),
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            })
        ]);
    }

    /**
     * Obter contagem de notificações não lidas
     */
    public function unreadCount()
    {
        $count = $this->notificationService->getUnreadCount(auth()->id());

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Marcar notificação como lida
     */
    public function markAsRead(Notification $notification)
    {
        // Verificar se a notificação pertence ao usuário autenticado
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificação marcada como lida'
        ]);
    }

    /**
     * Marcar todas as notificações como lidas
     */
    public function markAllAsRead()
    {
        $count = $this->notificationService->markAllAsRead(auth()->id());

        return response()->json([
            'success' => true,
            'message' => "{$count} notificações marcadas como lidas",
            'count' => $count
        ]);
    }

    /**
     * Inscrever para notificações push
     */
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'endpoint' => 'required',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required'
        ]);

        $endpoint = $request->endpoint;
        $key = $request->keys['p256dh'];
        $token = $request->keys['auth'];
        $contentEncoding = $request->contentEncoding ?? 'aesgcm';

        auth()->user()->updatePushSubscription($endpoint, $key, $token, $contentEncoding);

        return response()->json(['success' => true]);
    }

    /**
     * Cancelar inscrição de notificações push
     */
    public function unsubscribe(Request $request)
    {
        $this->validate($request, [
            'endpoint' => 'required'
        ]);

        auth()->user()->deletePushSubscription($request->endpoint);

        return response()->json(['success' => true]);
    }
}
