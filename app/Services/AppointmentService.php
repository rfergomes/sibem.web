<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentNotification;

class AppointmentService
{
    /**
     * Create a new appointment
     */
    public function create(array $data)
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'previsao';

        return Appointment::create($data);
    }

    /**
     * Update appointment status with logic for notifications
     */
    public function updateStatus(Appointment $appointment, string $newStatus, ?string $justification = null)
    {
        $oldStatus = $appointment->status;
        $appointment->status = $newStatus;
        $appointment->justification = $justification;
        $appointment->action_user_id = auth()->id();
        $appointment->save();

        if ($newStatus === 'confirmado') {
            $this->notifyConfirmation($appointment);
        }

        if ($newStatus === 'cancelado' || $newStatus === 'adiado') {
            $this->notifyChange($appointment, $newStatus);
        }

        return $appointment;
    }

    /**
     * Resolve the display name for the appointment location (church or admin).
     */
    protected function getLocalName(Appointment $appointment): string
    {
        if ($appointment->igreja) {
            return $appointment->igreja->nome;
        }
        return $appointment->local->nome ?? 'Local Desconhecido';
    }

    /**
     * Notify administrators about confirmation
     */
    protected function notifyConfirmation(Appointment $appointment)
    {
        $title = "📅 Agendamento Confirmado";
        $dateStr = $appointment->scheduled_at ? $appointment->scheduled_at->format('d/m/Y H:i') : 'Data não informada';
        $localName = $this->getLocalName($appointment);
        $message = "Inventário na localidade {$localName} confirmado para {$dateStr}";
        $link = route('appointments.index');

        $users = User::all(); // TODO: Filtrar usuários relevantes

        foreach ($users as $user) {
            $user->notify(new GeneralNotification($title, $message, $link));
        }

        // Enviar E-mail para a administração
        try {
            Mail::to($users)->send(new AppointmentNotification($appointment, 'confirmado'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send confirmation email: ' . $e->getMessage());
        }
    }

    /**
     * Notify about cancellation or postponement
     */
    protected function notifyChange(Appointment $appointment, string $status)
    {
        $statusLabel = $status === 'cancelado' ? 'Cancelado' : 'Adiado';
        $title = "⚠️ Agendamento {$statusLabel}";
        $localName = $this->getLocalName($appointment);
        $message = "O agendamento para {$localName} foi {$status}. Motivo: {$appointment->justification}";

        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new GeneralNotification($title, $message, '#'));
        }
    }

    /**
     * Generate WhatsApp confirmation message for the responsible contact.
     */
    public function generateWhatsAppMessage(Appointment $appointment): string
    {
        if (!$appointment->scheduled_at) {
            return "Erro: Data do agendamento não definida.";
        }

        $date = $appointment->scheduled_at->format('d/m/Y');
        $time = $appointment->scheduled_at->format('H:i');
        $local = $this->getLocalName($appointment);
        $responsavel = $appointment->responsavel_nome;

        $message = "A Paz de Deus, irmão {$responsavel}! 🤝\n\n";
        $message .= "Conforme alinhado anteriormente, gostaríamos de confirmar o inventário na casa de oração *{$local}*, conforme data e horário abaixo:\n\n";
        $message .= "📅 *Data:* {$date}\n";
        $message .= "⏰ *Horário:* {$time}h\n\n";
        $message .= "Poderia nos confirmar se este horário está disponível para nos acompanhar?\n\nDeus vos abençoe grandemente!";

        return $message;
    }

    /**
     * Generate WhatsApp invite message to share with church members.
     */
    public function generateInviteMessage(Appointment $appointment): string
    {
        if (!$appointment->scheduled_at) {
            return "Erro: Data do agendamento não definida.";
        }

        $date = $appointment->scheduled_at->format('d/m/Y');
        $time = $appointment->scheduled_at->format('H:i');
        $local = $this->getLocalName($appointment);

        $message = "A Paz de Deus, irmãos! 🙏\n\n";
        $message .= "Na preparação de Deus, estaremos realizando mais um *Inventário de Bens Móveis* e gostaríamos de convidá-los a participar deste momento de serviço na obra de Deus.\n\n";
        $message .= "⛪ *Local:* {$local}\n";
        $message .= "📅 *Data:* {$date}\n";
        $message .= "⏰ *Horário:* {$time}h\n\n";
        $message .= "Havendo disponibilidade, contamos com a presença de todos para que o trabalho do Senhor seja feito com dedicação! 🙌\n\nDeus vos abençoe grandemente!";

        return $message;
    }
}
