<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Agendamento;
use App\Models\Igreja;
use App\Models\Local;
use App\Models\User;

class AgendamentoController extends Controller
{
    /**
     * Check permissions and access scopes.
     */
    private function checkAccess($action, $targetAdmlcId = null)
    {
        $user = Auth::user();
        
        // View-only check for auditors
        if ($action !== 'view') {
            if ($user->isAuditor()) {
                abort(403, 'Acesso não autorizado. Auditores possuem apenas permissão de leitura.');
            }
        }

        // Scope check on the local administration
        if ($targetAdmlcId !== null && !$user->isAdminSistema()) {
            if ($user->isAdminRegional()) {
                $local = Local::find($targetAdmlcId);
                if (!$local || $local->admrg_id != $user->regional_id) {
                    abort(403, 'Acesso não autorizado. A localidade não pertence à sua Regional.');
                }
            } else { // admin_local, operador, auditor
                $activeLocalId = session()->get('active_admlc_id') ?? $user->admlc_id;
                if ($targetAdmlcId != $activeLocalId) {
                    abort(403, 'Acesso não autorizado para esta localidade.');
                }
            }
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->checkAccess('view');
        
        // Determine the active local ID
        $activeLocalId = session()->get('active_admlc_id') ?? $user->admlc_id;

        // Base query
        $query = Agendamento::with(['igreja', 'local', 'operator']);

        // Apply permissions scopes
        if ($user->isAdminSistema()) {
            if ($request->filled('admlc_id')) {
                $query->where('admlc_id', $request->admlc_id);
            }
        } elseif ($user->isAdminRegional()) {
            $regionalId = $user->regional_id;
            $query->whereHas('local', function($q) use ($regionalId) {
                $q->where('admrg_id', $regionalId);
            });
            if ($request->filled('admlc_id')) {
                $local = Local::find($request->admlc_id);
                if ($local && $local->admrg_id == $regionalId) {
                    $query->where('admlc_id', $request->admlc_id);
                }
            }
        } else {
            $query->where('admlc_id', $activeLocalId);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter (responsavel, acompanhante, etc.)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('responsavel_nome', 'like', "%{$search}%")
                  ->orWhere('acompanhante_nome', 'like', "%{$search}%")
                  ->orWhereHas('igreja', function($qi) use ($search) {
                      $qi->where('igreja', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->ajax() || $request->wantsJson() || ($request->has('start') && $request->has('end'))) {
            if ($request->filled('start') && $request->filled('end')) {
                $query->whereBetween('data', [$request->start, $request->end]);
            }
            
            $agendamentos = $query->get();

            $events = $agendamentos->map(function($a) {
                // Status colors
                $color = '#f1c40f'; // Pendente - Yellow
                if ($a->status === 'Confirmado') {
                    $color = '#2ecc71'; // Green
                } elseif ($a->status === 'Reagendado') {
                    $color = '#3498db'; // Blue
                } elseif ($a->status === 'Cancelado') {
                    $color = '#e74c3c'; // Red
                }

                return [
                    'id' => $a->id,
                    'title' => ($a->igreja ? $a->igreja->igreja : 'Igreja não encontrada'),
                    'start' => $a->data . 'T' . $a->horario,
                    'color' => $color,
                    'extendedProps' => [
                        'responsavel_nome' => $a->responsavel_nome,
                        'responsavel_telefone' => $a->responsavel_telefone,
                        'acompanhante_nome' => $a->acompanhante_nome,
                        'admlc_id' => $a->admlc_id,
                        'local_nome' => $a->local ? $a->local->adm_local : 'N/A',
                        'status' => $a->status,
                        'observacao' => $a->observacao,
                        'motivo_cancelamento' => $a->motivo_cancelamento,
                        'data' => date('d/m/Y', strtotime($a->data)),
                        'data_raw' => $a->data,
                        'horario' => substr($a->horario, 0, 5),
                        'igreja_nome' => $a->igreja ? $a->igreja->igreja : '',
                        'igreja_id' => $a->igreja_id,
                        'operador_nome' => $a->operator ? $a->operator->name : 'N/A'
                    ]
                ];
            });

            return response()->json($events);
        }

        // Standard HTML request: paginated list view
        $agendamentos = $query->orderBy('data', 'asc')->orderBy('horario', 'asc')->paginate(15)->withQueryString();

        // Fetch lists for select dropdowns in view (scoped to user's permission)
        $locais = $user->getAvailableLocais();

        // Fetch churches and users scoped to user's permissions for the creation modal
        if ($user->isAdminSistema() || $user->isAdminRegional()) {
            $igrejas = collect();
            $usuarios = collect();
        } else {
            $igrejas = Igreja::where('admlc_id', $activeLocalId)->orderBy('igreja')->get();
            $usuarios = User::where('admlc_id', $activeLocalId)->orderBy('name')->get();
        }

        return view('agendamentos.index', compact('agendamentos', 'locais', 'igrejas', 'usuarios', 'activeLocalId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAccess('create');

        $validated = $request->validate([
            'igreja_id' => 'required|exists:mysql_sys.igrejas_v2,id',
            'responsavel_nome' => 'required|string|max:200',
            'responsavel_telefone' => 'nullable|string|max:30',
            'acompanhante_nome' => 'nullable|string|max:200',
            'data' => 'required|date',
            'horario' => 'required|date_format:H:i',
            'status' => 'required|string|in:Pendente,Confirmado',
            'observacao' => 'nullable|string',
        ]);

        $igreja = Igreja::findOrFail($validated['igreja_id']);

        // Check if user is authorized to create schedules for this church's local
        $this->checkAccess('create', $igreja->admlc_id);

        $validated['admlc_id'] = $igreja->admlc_id;
        $validated['user_id'] = Auth::id();

        Agendamento::create($validated);

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento registrado com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $this->checkAccess('edit', $agendamento->admlc_id);

        $validated = $request->validate([
            'responsavel_nome' => 'required|string|max:200',
            'responsavel_telefone' => 'nullable|string|max:30',
            'acompanhante_nome' => 'nullable|string|max:200',
            'observacao' => 'nullable|string',
        ]);

        $agendamento->update($validated);

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento atualizado com sucesso.');
    }

    /**
     * Re-schedule a schedule.
     */
    public function reagendar(Request $request, $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $this->checkAccess('edit', $agendamento->admlc_id);

        $validated = $request->validate([
            'data' => 'required|date',
            'horario' => 'required|date_format:H:i',
            'observacao' => 'nullable|string',
        ]);

        $oldData = date('d/m/Y', strtotime($agendamento->data)) . ' às ' . substr($agendamento->horario, 0, 5);
        
        $agendamento->data = $validated['data'];
        $agendamento->horario = $validated['horario'];
        $agendamento->status = 'Reagendado';

        $newData = date('d/m/Y', strtotime($validated['data'])) . ' às ' . $validated['horario'];
        $timestamp = date('d/m/Y H:i');
        $user = Auth::user()->name;
        $log = "\n[{$timestamp} - Reagendado por {$user}] De: {$oldData} Para: {$newData}";
        if ($request->filled('observacao')) {
            $log .= ". Justificativa: " . $request->observacao;
        }
        
        $agendamento->observacao = $agendamento->observacao . $log;
        $agendamento->save();

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento reagendado com sucesso.');
    }

    /**
     * Cancel a schedule.
     */
    public function cancelar(Request $request, $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $this->checkAccess('edit', $agendamento->admlc_id);

        $validated = $request->validate([
            'motivo_cancelamento' => 'required|string',
        ]);

        $agendamento->status = 'Cancelado';
        $agendamento->motivo_cancelamento = $validated['motivo_cancelamento'];
        
        $timestamp = date('d/m/Y H:i');
        $user = Auth::user()->name;
        $log = "\n[{$timestamp} - Cancelado por {$user}] Motivo: " . $validated['motivo_cancelamento'];
        $agendamento->observacao = $agendamento->observacao . $log;
        $agendamento->save();

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento cancelado com sucesso.');
    }

    /**
     * Confirm a schedule.
     */
    public function confirmar($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $this->checkAccess('edit', $agendamento->admlc_id);

        $agendamento->status = 'Confirmado';
        
        $timestamp = date('d/m/Y H:i');
        $user = Auth::user()->name;
        $log = "\n[{$timestamp} - Confirmado por {$user}]";
        $agendamento->observacao = $agendamento->observacao . $log;
        $agendamento->save();

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento confirmado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $this->checkAccess('delete', $agendamento->admlc_id);

        $agendamento->delete();

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento excluído com sucesso.');
    }

    /**
     * Get churches and users by administration local ID for AJAX populating.
     */
    public function getDadosPorLocal($admlcId)
    {
        $user = Auth::user();
        
        // Scope security check
        if (!$user->isAdminSistema()) {
            if ($user->isAdminRegional()) {
                $local = Local::find($admlcId);
                if (!$local || $local->admrg_id != $user->regional_id) {
                    return response()->json([], 403);
                }
            } else {
                $activeLocalId = session()->get('active_admlc_id') ?? $user->admlc_id;
                if ($admlcId != $activeLocalId) {
                    return response()->json([], 403);
                }
            }
        }

        $igrejas = Igreja::where('admlc_id', $admlcId)
            ->orderBy('igreja')
            ->get(['id', 'igreja', 'cod_siga']);

        $usuarios = User::where('admlc_id', $admlcId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'igrejas' => $igrejas,
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Get next confirmed schedules for a specific local administration.
     */
    public function getProximosConfirmados($admlcId)
    {
        $user = Auth::user();
        
        // Scope security check
        if (!$user->isAdminSistema()) {
            if ($user->isAdminRegional()) {
                $local = Local::find($admlcId);
                if (!$local || $local->admrg_id != $user->regional_id) {
                    return response()->json([], 403);
                }
            } else {
                $activeLocalId = session()->get('active_admlc_id') ?? $user->admlc_id;
                if ($admlcId != $activeLocalId) {
                    return response()->json([], 403);
                }
            }
        }

        $proximos = Agendamento::with('igreja')
            ->where('admlc_id', $admlcId)
            ->where('status', 'Confirmado')
            ->where('data', '>=', date('Y-m-d'))
            ->orderBy('data', 'asc')
            ->orderBy('horario', 'asc')
            ->get();

        return response()->json([
            'proximos' => $proximos
        ]);
    }
}
