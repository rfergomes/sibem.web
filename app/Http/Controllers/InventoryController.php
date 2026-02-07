<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\InventarioDetalhe;
use App\Models\Bem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $currentLocal = session('current_local_id') ?? Auth::user()->local_id;

        $inventarios = Inventario::where('id_igreja', '>', 0) // Should be scoped by user permissions eventually
            ->orderBy('ano', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        // For the creation form
        $setores = DB::connection('mysql')->table('igrejas_global')
            ->where('local_id', $currentLocal)
            ->whereNotNull('setor')
            ->distinct()
            ->pluck('setor');

        $igrejas = DB::connection('mysql')->table('igrejas_global')
            ->where('local_id', $currentLocal)
            ->get();

        if ($igrejas->isEmpty() && Auth::id()) {
            // Debug fallback: if still empty, let's see if the user has any authorized locales
            $authorized = Auth::user()->authorized_locais->pluck('id')->first();
            if ($authorized && $authorized != $currentLocal) {
                $currentLocal = $authorized;
                // Re-query
                $igrejas = DB::connection('mysql')->table('igrejas_global')->where('local_id', $currentLocal)->get();
                $setores = DB::connection('mysql')->table('igrejas_global')->where('local_id', $currentLocal)->whereNotNull('setor')->distinct()->pluck('setor');
            }
        }

        $usuarios = \App\Models\User::orderBy('nome')->get(['id', 'nome']);

        return view('inventarios.index', compact('inventarios', 'setores', 'igrejas', 'usuarios'));
    }

    public function create()
    {
        return redirect()->route('inventarios.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ano' => 'required|integer|min:2024',
            'mes' => 'required|integer|min:1|max:12',
            'igreja_id' => 'required|integer',
            'responsavel' => 'required|string',
            'inventariante' => 'required|string',
        ]);

        $currentLocal = session('current_local_id');

        // Check if there is already an open inventory for this church
        $open = Inventario::where('id_igreja', $request->igreja_id)
            ->where('status', 'aberto')
            ->first();

        if ($open) {
            return redirect()->back()->with('error', 'Já existe um inventário aberto para esta localidade.');
        }

        DB::connection('tenant')->beginTransaction();

        try {
            $inventario = Inventario::create([
                'ano' => $request->ano,
                'mes' => $request->mes,
                'codigo_unico' => "INV-{$request->igreja_id}-" . now()->timestamp,
                'id_igreja' => $request->igreja_id,
                'status' => 'aberto',
                'is_sincronizado' => false,
                'user_id_abertura' => Auth::id(),
                'responsavel' => $request->responsavel,
                'inventariante' => $request->inventariante
            ]);

            DB::connection('tenant')->commit();
            return redirect()->route('inventarios.show', $inventario->id)
                ->with('success', 'Inventário aberto! Agora realize a importação obrigatória.');

        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return redirect()->back()->with('error', 'Erro ao abrir inventário: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $inventario = Inventario::with(['detalhes.bem'])->findOrFail($id);
        return view('inventarios.show', compact('inventario'));
    }

    public function finalize($id)
    {
        $inventario = Inventario::findOrFail($id);

        if (!$inventario->is_sincronizado) {
            return redirect()->back()->with('error', 'O inventário deve ser sincronizado antes de finalizar.');
        }

        if ($inventario->status !== 'aberto') {
            return redirect()->back()->with('error', 'Este inventário já está finalizado ou auditado.');
        }

        // CRITICAL VALIDATION: Check for items without tratativas
        $untreatedCount = $inventario->detalhes()
            ->whereIn('status_leitura', ['nao_encontrado', 'novo_sistema'])
            ->where(function ($q) {
                $q->whereNull('tratativa')
                    ->orWhere('tratativa', 'nenhuma');
            })
            ->count();

        if ($untreatedCount > 0) {
            return redirect()->back()->with(
                'error',
                "ATENÇÃO: Existem {$untreatedCount} itens pendentes sem tratativa definida. " .
                "Todos os itens devem ter uma decisão (Excluir, Transferir, Imprimir, etc.) antes da finalização. " .
                "Acesse o modal de 'Pendências' na tela de conferência para tratar os itens restantes."
            );
        }

        $inventario->status = 'fechado';
        $inventario->save();

        return redirect()->route('inventarios.show', $inventario->id)
            ->with('success', 'Inventário finalizado com sucesso! O relatório oficial foi gerado.');
    }

    public function printReport($id)
    {
        $inventario = Inventario::findOrFail($id);
        return view('inventarios.report_print', compact('inventario'));
    }

    public function customReport($id)
    {
        $inventario = Inventario::findOrFail($id);

        // Calculate result for header (Strict Audit logic)
        $bensInicial = $inventario->detalhes()->where('status_leitura', '!=', 'novo_sistema')->count();
        $localizados = $inventario->detalhes()->where('status_leitura', 'encontrado')->count();
        $novos = $inventario->detalhes()->where('status_leitura', 'novo_sistema')->count();

        $bensFinal = $localizados + $novos;
        $resultado = ($bensInicial > 0) ? ($bensFinal / $bensInicial) * 100 : 0;

        return view('inventarios.report_custom', compact('inventario', 'resultado'));
    }
}
