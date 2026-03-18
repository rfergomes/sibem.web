<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\ContactMessage;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
        ]);

        // Envia e-mail via SMTP (credenciais definidas no .env)
        try {
            Mail::to(config('mail.from.address'))
                ->send(new ContactMail($contact));
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar e-mail de contato: " . $e->getMessage());
        }

        try {
            $this->notificationService->createLandingContactNotification($contact);
        } catch (\Exception $e) {
            \Log::error("Erro ao notificar contato landing page: " . $e->getMessage());
        }

        // Return "OK" to satisfy the template's validate.js
        return response('OK');
    }
}
