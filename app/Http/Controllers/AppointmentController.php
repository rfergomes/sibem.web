<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Local;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Display a listing of appointments.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['local', 'creator', 'igreja'])
            ->orderBy('scheduled_at', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('scheduled_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('scheduled_at', $request->year);
        }

        $appointments = $query->paginate(15);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        // Get user's authorized Locais
        $authorizedLocais = auth()->user()->authorized_locais;
        $locaisIds = $authorizedLocais->pluck('id');

        // Fetch Churches belonging to these Locais with relationships
        $igrejas = \App\Models\Igreja::whereIn('local_id', $locaisIds)
            ->with(['local'])
            ->orderBy('local_id')
            ->orderBy('nome')
            ->get()
            ->map(function ($igreja) {
                return [
                    'id' => $igreja->id,
                    'nome' => $igreja->nome,
                    'cod_siga' => $igreja->cod_siga, // Use formatting logic from accessor? No, raw or accessor? Accessor is getCodSigaAttribute
                    'local_id' => $igreja->local_id,
                    'local_nome' => $igreja->local->nome ?? 'N/A',
                    'setor' => $igreja->setor, // Assuming 'setor' is a string column on igrejas_global
                    'full_name' => ($igreja->cod_siga ? $igreja->cod_siga . ' - ' : '') . $igreja->nome . ' (' . ($igreja->local->nome ?? 'N/A') . ')'
                ];
            });

        $locais = $authorizedLocais->map(function ($local) {
            return ['id' => $local->id, 'nome' => $local->nome];
        });

        // Get unique Setores from the fetched igrejas
        $setores = $igrejas->pluck('setor')->unique()->filter()->values();

        return view('appointments.create', compact('igrejas', 'locais', 'setores'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'igreja_id' => 'required|exists:igrejas_global,id',
            'responsavel_nome' => 'required|string|max:255',
            'responsavel_cargo' => 'nullable|string|max:255',
            'responsavel_contato' => 'nullable|string|max:255',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Resolve Local ID from Church
        $igreja = \App\Models\Igreja::find($validated['igreja_id']);
        $validated['local_id'] = $igreja->local_id;

        $this->appointmentService->create($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento em previsão criado com sucesso!');
    }

    /**
     * Update the status of an appointment.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:previsao,confirmado,cancelado,adiado',
            'justification' => 'required_if:status,cancelado,adiado|nullable|string',
            'new_date' => 'nullable|date',
        ]);

        // Logic for Rescheduling (Adiado -> Previsão with new date)
        if ($validated['status'] === 'adiado' && !empty($validated['new_date'])) {
            $appointment->scheduled_at = $validated['new_date'];
            $appointment->status = 'previsao'; // Reset to forecast
            $appointment->justification = "Reagendado: " . ($validated['justification'] ?? 'Sem justificativa');
        } else {
            $appointment->status = $validated['status'];
            if (isset($validated['justification'])) {
                $appointment->justification = $validated['justification'];
            }
        }

        $appointment->save();

        // Auto-send notifications when status is confirmed
        if (in_array($validated['status'], ['confirmado', 'cancelado', 'adiado'])) {
            $this->appointmentService->updateStatus(
                $appointment,
                $appointment->status, // already saved above
                $appointment->justification
            );
        }

        return back()->with('success', 'Agendamento atualizado com sucesso!');
    }

    /**
     * Show a specific appointment and WhatsApp generator.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['local', 'igreja']);
        $whatsappMessage = $this->appointmentService->generateWhatsAppMessage($appointment);
        $inviteMessage = $this->appointmentService->generateInviteMessage($appointment);
        return view('appointments.show', compact('appointment', 'whatsappMessage', 'inviteMessage'));
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        // Prevent deleting confirmed appointments
        if ($appointment->status === 'confirmado') {
            return back()->with('error', 'Não é possível excluir um agendamento confirmado. Cancele ou reagende primeiro.');
        }

        try {
            // 1. Try to delete from the model's connection (Tenant)
            $deleted = \Illuminate\Support\Facades\DB::connection($appointment->getConnectionName())
                ->table('appointments')
                ->where('id', $appointment->id)
                ->delete();

            // 2. If failed (0 rows), try default connection (Landlord/System) as fallback
            if ($deleted === 0) {
                \Illuminate\Support\Facades\Log::warning('Delete on model connection failed. Trying default connection for ID: ' . $appointment->id);
                $deleted = \Illuminate\Support\Facades\DB::table('appointments')
                    ->where('id', $appointment->id)
                    ->delete();
            }

            if ($deleted) {
                \Illuminate\Support\Facades\Log::info('Successfully deleted appointment ID: ' . $appointment->id);
            } else {
                \Illuminate\Support\Facades\Log::error('CRITICAL: Delete failed on ALL connections for ID: ' . $appointment->id);
                // Return error to user so they know it failed
                return back()->with('error', 'Erro CRÍTICO: Não foi possível localizar o registro para exclusão. ID: ' . $appointment->id);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception during delete: ' . $e->getMessage());
            return back()->with('error', 'Erro ao excluir agendamento: ' . $e->getMessage());
        }

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento removido.');
    }
}
