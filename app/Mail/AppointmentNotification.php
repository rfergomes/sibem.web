<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $type; // confirmado, adiado, cancelado

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, string $type)
    {
        $this->appointment = $appointment;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function __construct_envelope(): Envelope
    {
        $subjects = [
            'confirmado' => '📅 [SIBEM] Agendamento de Inventário Confirmado',
            'adiado' => '⏳ [SIBEM] Agendamento de Inventário ADIADO/REAGENDADO',
            'cancelado' => '⚠️ [SIBEM] Agendamento de Inventário CANCELADO',
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? '[SIBEM] Notificação de Agendamento',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
