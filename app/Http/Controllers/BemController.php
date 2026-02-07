<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bem;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BensImport;
use Illuminate\Support\Facades\Session;

class BemController extends Controller
{
    public function index(Request $request)
    {
        $query = Bem::with(['dependencia.setor']);

        // Filtro por Código
        if ($request->filled('codigo')) {
            $query->where('id_bem', 'like', '%' . $request->codigo . '%');
        }

        // Filtro por Descrição
        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        // Filtro por Status
        if ($request->filled('status')) {
            $query->where('id_status', $request->status);
        }

        // Filtro por Dependência
        if ($request->filled('dependencia_id')) {
            $query->where('id_dependencia', $request->dependencia_id);
        }

        // Filtro por Setor (via relacionamento)
        if ($request->filled('setor_id')) {
            $query->whereHas('dependencia', function ($q) use ($request) {
                $q->where('setor_id', $request->setor_id);
            });
        }

        $bens = $query->paginate(15)->withQueryString();

        // Dados para os selects de filtro
        $setores = \App\Models\Setor::orderBy('nome')->get();
        // Carrega dependências apenas se um setor for selecionado (opcional, mas aqui carrego todas para simplificar)
        $dependencias = \App\Models\Dependencia::orderBy('nome')->get();

        return view('bens.index', compact('bens', 'setores', 'dependencias'));
    }

    public function showImportForm()
    {
        return view('bens.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'inventario_id' => 'nullable|integer'
        ]);

        try {
            set_time_limit(600);
            ini_set('memory_limit', '512M');

            $inventarioId = $request->inventario_id;
            Excel::import(new BensImport($inventarioId), $request->file('file'));

            if ($inventarioId) {
                $inventario = \App\Models\Inventario::find($inventarioId);
                if ($inventario) {
                    $globalChurch = \Illuminate\Support\Facades\DB::connection('mysql')
                        ->table('igrejas_global')
                        ->where('id', $inventario->id_igreja)
                        ->first();

                    if ($globalChurch && $globalChurch->codigo_ccb) {
                        $churchCode = str_replace(['-', ' '], '', $globalChurch->codigo_ccb);

                        // Populate inventario_detalhes with ONLY ACTIVE bens for this specific church code
                        $bens = Bem::where('id_igreja', $churchCode)
                            ->where('id_status', 1) // Only active assets
                            ->get();

                        // Prepare data for bulk upsert
                        $detailsToUpsert = [];
                        foreach ($bens as $bem) {
                            $detailsToUpsert[] = [
                                'inventario_id' => $inventario->id,
                                'id_bem' => $bem->id_bem,
                                'status_leitura' => 'nao_encontrado',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        // Bulk Upsert (much faster than individual queries)
                        if (!empty($detailsToUpsert)) {
                            foreach (array_chunk($detailsToUpsert, 500) as $chunk) {
                                \App\Models\InventarioDetalhe::upsert(
                                    $chunk,
                                    ['inventario_id', 'id_bem'],
                                    ['status_leitura', 'updated_at']
                                );
                            }
                        }
                    }

                    $inventario->is_sincronizado = true;
                    $inventario->save();
                }
                return redirect()->route('inventarios.show', $inventarioId)->with('success', 'Sincronização concluída com sucesso!');
            }

            return redirect()->route('bens.index')->with('success', 'Importação concluída com sucesso!');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Illuminate\Support\Facades\Log::error('Erro importação Excel: ' . $errorMessage);
            return back()->with('error', 'Erro na importação: ' . $errorMessage);
        }
    }
}
