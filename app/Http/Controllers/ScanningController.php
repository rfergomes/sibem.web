<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\InventarioDetalhe;
use App\Models\Bem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ScanningController extends Controller
{
    /**
     * Show the Scanning Interface (Conference Mode).
     */
    public function show($id)
    {
        $inventario = Inventario::with(['detalhes.bem'])->findOrFail($id);

        // Stats calculation (Strict Rules)
        // 1. Initial Assets (Those that were in the SIGA sync)
        $bensInicial = $inventario->detalhes()
            ->where('status_leitura', '!=', 'novo_sistema')
            ->count();

        // 2. Located Assets (From the initial list)
        $localizados = $inventario->detalhes()
            ->where('status_leitura', 'encontrado')
            ->count();

        // 3. New Assets (Scanned during inventory, not in initial list)
        $novos = $inventario->detalhes()
            ->where('status_leitura', 'novo_sistema')
            ->count();

        $pendentes = $inventario->detalhes()
            ->where('status_leitura', 'nao_encontrado')
            ->count();

        $prevista = ($bensInicial > 0) ? round(($localizados / $bensInicial) * 100, 2) : 0;
        $bensFinal = $localizados + $novos;
        $resultado = ($bensInicial > 0) ? round(($bensFinal / $bensInicial) * 100, 2) : 0;

        // Tratativa counts
        $tratativaCounts = [
            'imprimir' => $inventario->detalhes()->where('tratativa', 'imprimir')->count(),
            'alterar' => $inventario->detalhes()->where('tratativa', 'alterar')->count(),
            'excluir' => $inventario->detalhes()->where('tratativa', 'excluir')->count(),
            'transferir' => $inventario->detalhes()->where('tratativa', 'transferir')->count(),
        ];

        $ultimosLeituras = $inventario->detalhes()
            ->whereNotNull('timestamp_leitura')
            ->orderBy('timestamp_leitura', 'desc')
            ->take(50)
            ->with('bem')
            ->get();

        // Map recent readings for the frontend history list
        $historyInitial = $ultimosLeituras->map(function ($d) {
            return [
                'barcode' => $d->id_bem,
                'descricao' => $d->bem ? $d->bem->descricao : 'ITEM NÃO ENCONTRADO',
                'dependencia' => $d->bem ? $d->bem->id_dependencia : '---',
                'situacao' => $d->status_leitura === 'encontrado' ? 'CONFERIDO' : ($d->status_leitura === 'nao_encontrado' ? 'PENDENTE' : 'DIVERGÊNCIA'),
                'is_cross_church' => str_contains($d->observacao ?? '', 'LOCALIDADE'),
                'lido' => true
            ];
        });

        // Pass all pendencies for the modal
        $allDetalhes = $inventario->detalhes()->with('bem')->get();

        return view('inventarios.scan', compact(
            'inventario',
            'bensInicial',
            'localizados',
            'pendentes',
            'prevista',
            'novos',
            'bensFinal',
            'resultado',
            'tratativaCounts',
            'historyInitial',
            'allDetalhes'
        ));
    }

    public function saveTratativa(Request $request, $id)
    {
        $request->validate([
            'detalhe_ids' => 'required|array',
            'detalhe_ids.*' => 'integer',
            'tratativa' => 'required|string',
            'observacao' => 'nullable|string',
        ]);

        $ids = $request->detalhe_ids;
        $tratativa = $request->tratativa;
        $observacao = $request->observacao;

        DB::connection('tenant')->beginTransaction();

        try {
            foreach ($ids as $detalheId) {
                $detalhe = InventarioDetalhe::where('inventario_id', $id)
                    ->where('id', $detalheId)
                    ->firstOrFail();

                $detalhe->tratativa = $tratativa;
                $detalhe->observacao = $observacao;

                // If found through tratativa
                if ($tratativa === 'encontrado' || $tratativa === 'novo') {
                    $detalhe->status_leitura = 'encontrado';
                    $detalhe->timestamp_leitura = now();
                    $detalhe->user_id_conferencia = Auth::id();

                    // If it's a new asset (Divergence override) - Only for single item registration usually
                    // but we keep logic for backward compatibility or individual "new" marks
                    if ($tratativa === 'novo' && $request->has('nova_descricao')) {
                        // Update the Bem record if it's a shadow record
                        $bem = $detalhe->bem;
                        if ($bem && $bem->origem === 'scanner_manual') {
                            $bem->update([
                                'descricao' => strtoupper($request->nova_descricao),
                                'id_dependencia' => $request->nova_dependencia,
                                'id_status' => 1 // Active
                            ]);
                        }

                        // Update observation with new data
                        $prefix = " [NOVO BEM REGISTRADO: " . strtoupper($request->nova_descricao) . " em DEP {$request->nova_dependencia}]";
                        $detalhe->observacao = ($detalhe->observacao ?: "") . $prefix;

                        // Create divergência record for new item
                        \App\Models\Divergencia::create([
                            'inventario_id' => $id,
                            'id_bem' => $detalhe->id_bem,
                            'codigo_divergencia' => '01',
                            'descricao' => 'NOVO BEM REGISTRADO: ' . strtoupper($request->nova_descricao),
                            'id_dependencia_anterior' => null,
                            'id_dependencia_nova' => $request->nova_dependencia,
                            'registrado_por' => Auth::user()->nome
                        ]);

                        // Generate donation PDFs if marked as donation
                        if ($request->is_doacao) {
                            $detalhe->is_doacao = true;

                            // Prepare data for PDF
                            $inventario = Inventario::findOrFail($id);
                            $dependencia = \App\Models\Dependencia::find($request->nova_dependencia);

                            $pdfData = [
                                'dataEmissao' => now()->format('d/m/Y'),
                                'administracao' => 'CNPJ da Administração',
                                'cidade' => $dependencia ? $dependencia->nome : 'N/A',
                                'setor' => $inventario->id_dependencia ?? 'N/A',
                                'descricaoBem' => strtoupper($request->nova_descricao),
                                'localData' => ($dependencia ? $dependencia->nome : 'N/A') . ', ' . now()->format('d/m/Y'),
                                'idBem' => $detalhe->id_bem,
                                'dependencia' => $dependencia ? $dependencia->nome : 'N/A'
                            ];

                            // Create directory if not exists
                            $dir = storage_path("app/public/doacoes/{$id}");
                            if (!file_exists($dir)) {
                                mkdir($dir, 0755, true);
                            }

                            // Generate PDF 14.1
                            $pdf141 = Pdf::loadView('pdf.formulario_14_1', $pdfData);
                            $filename141 = "doacao_14.1_{$detalhe->id_bem}_" . time() . ".pdf";
                            $pdf141->save("{$dir}/{$filename141}");

                            // Generate PDF 14.2
                            $pdf142 = Pdf::loadView('pdf.formulario_14_2', $pdfData);
                            $filename142 = "doacao_14.2_{$detalhe->id_bem}_" . time() . ".pdf";
                            $pdf142->save("{$dir}/{$filename142}");

                            // Store path in database
                            $detalhe->documento_doacao_path = "doacoes/{$id}/{$filename141}|{$filename142}";
                        }
                    }
                }

                $detalhe->save();
            }

            DB::connection('tenant')->commit();
            $count = count($ids);

            // Check if any item has donation PDFs
            $donationPdfs = null;
            if ($request->is_doacao && $request->tratativa === 'novo') {
                $firstDetalhe = InventarioDetalhe::find($ids[0]);
                if ($firstDetalhe && $firstDetalhe->documento_doacao_path) {
                    $files = explode('|', $firstDetalhe->documento_doacao_path);
                    $donationPdfs = [
                        'form_14_1' => asset('storage/' . $files[0]),
                        'form_14_2' => asset('storage/' . $files[1])
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => $count > 1 ? "{$count} tratativas salvas com sucesso." : 'Tratativa salva com sucesso.',
                'donation_pdfs' => $donationPdfs
            ]);

        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Process a Barcode Scan.
     */
    public function searchByDescription(Request $request, $id)
    {
        $request->validate(['query' => 'required|string|min:3']);
        $query = mb_strtolower($request->input('query'));

        $inventario = Inventario::findOrFail($id);

        // 1. Search in items ALREADY assigned to this inventory
        $results = InventarioDetalhe::where('inventario_id', $inventario->id)
            ->whereHas('bem', function ($q) use ($query) {
                $q->where('descricao', 'like', "%{$query}%");
            })
            ->with('bem')
            ->get();

        // 2. If no direct inventory matches, search GLOBALLY in the current church (id_igreja)
        if ($results->count() === 0) {
            $globalBens = Bem::where('id_igreja', $inventario->id_igreja)
                ->where('descricao', 'like', "%{$query}%")
                ->take(10) // Limit global results for performance
                ->get();

            if ($globalBens->count() === 0) {
                return response()->json(['status' => 'not_found', 'message' => "Nenhum item com a descrição '{$query}' foi encontrado nesta localidade."]);
            }

            return response()->json([
                'status' => $globalBens->count() === 1 ? 'single_match' : 'multiple_matches',
                'is_global' => true,
                'items' => $globalBens->map(function ($b) {
                    return [
                        'id_bem' => $b->id_bem,
                        'descricao' => $b->descricao,
                        'detalhe_id' => null // Not in inventory yet
                    ];
                }),
                // For logic compatibility with single_match
                'id_bem' => $globalBens->count() === 1 ? $globalBens->first()->id_bem : null,
                'descricao' => $globalBens->count() === 1 ? $globalBens->first()->descricao : null,
            ]);
        }

        // 3. Return Inventory matches
        if ($results->count() === 1) {
            $detalhe = $results->first();
            return response()->json([
                'status' => 'single_match',
                'is_global' => false,
                'id_bem' => $detalhe->bem->id_bem,
                'descricao' => $detalhe->bem->descricao,
                'detalhe_id' => $detalhe->id
            ]);
        }

        return response()->json([
            'status' => 'multiple_matches',
            'is_global' => false,
            'items' => $results->map(function ($d) {
                return [
                    'id_bem' => $d->bem->id_bem,
                    'descricao' => $d->bem->descricao,
                    'detalhe_id' => $d->id
                ];
            })
        ]);
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'barcode' => 'required|string',
            'id_dependencia_atual' => 'nullable|integer'
        ]);

        $barcode = trim($request->barcode);
        $inventario = Inventario::findOrFail($id);
        $dependenciaLeitura = $request->id_dependencia_atual;

        // 0. Church ID Validation (Warning only, as requested)
        $prefix = substr($barcode, 0, 6);
        $churchWarning = null;
        if ($prefix != $inventario->id_igreja) {
            $churchWarning = "⚠️ ATENÇÃO: BEM PERTENCE À LOCALIDADE ID {$prefix}!";
        }

        // 1. Search in Inventory Details (Pre-populated)
        $detalhe = InventarioDetalhe::where('inventario_id', $inventario->id)
            ->where('id_bem', $barcode)
            ->first();

        if ($detalhe) {
            $bem = $detalhe->bem;

            if ($detalhe->status_leitura === 'encontrado') {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Bem já conferido anteriormente!',
                    'bem' => $bem
                ]);
            }

            // Check for Dependency Divergence
            if ($dependenciaLeitura && $bem->id_dependencia != $dependenciaLeitura) {
                \App\Models\Divergencia::create([
                    'inventario_id' => $inventario->id,
                    'id_bem' => $bem->id_bem,
                    'codigo_divergencia' => '03',
                    'descricao' => "Transferência física detectada: De {$bem->id_dependencia} para {$dependenciaLeitura}",
                    'id_dependencia_anterior' => $bem->id_dependencia,
                    'id_dependencia_nova' => $dependenciaLeitura,
                    'registrado_por' => Auth::user()->nome
                ]);
            }

            // Mark as found
            $detalhe->status_leitura = 'encontrado';
            $detalhe->user_id_conferencia = Auth::id();
            $detalhe->timestamp_leitura = now();
            if ($churchWarning) {
                $detalhe->observacao = ($detalhe->observacao ? $detalhe->observacao . " | " : "") . $churchWarning;
            }
            $detalhe->save();

            return response()->json([
                'status' => 'success',
                'message' => $churchWarning ?: 'Item conferido com sucesso.',
                'bem' => $bem,
                'is_cross_church' => !!$churchWarning
            ]);
        }

        // 2. Not in details -> Divergence (Items in CO not in report)
        $bem = Bem::find($barcode);

        // If the barcode is not in the system AT ALL, we create a shadow record to satisfy foreign keys
        // as requested: "se houver um bem novo ainda não registrado no ERP, inserir como bem novo"
        if (!$bem) {
            $bem = Bem::create([
                'id_bem' => $barcode,
                'descricao' => 'BEM NÃO CADASTRADO - [PENDENTE DE IDENTIFICAÇÃO]',
                'id_igreja' => $inventario->id_igreja,
                'id_dependencia' => $dependenciaLeitura ?: 102, // Default to Almoxarifado if unknown
                'id_status' => 1, // Assumed active/draft
                'origem' => 'scanner_manual'
            ]);
        }

        $codigo = '02';
        $msg = $bem->origem === 'scanner_manual' ? 'NOVO BEM DETECTADO (Não consta no SIGA)' : 'Bem catalogado mas fora da lista inicial.';

        if ($churchWarning) {
            $msg = $churchWarning . " " . $msg;
        }

        // Create Divergencia record
        \App\Models\Divergencia::create([
            'inventario_id' => $inventario->id,
            'id_bem' => $barcode,
            'codigo_divergencia' => $codigo,
            'descricao' => $msg,
            'id_dependencia_nova' => $dependenciaLeitura,
            'registrado_por' => Auth::user()->nome
        ]);

        // Create InventarioDetalhe (Foreign key is now satisfied by the 'shadow' Bem)
        $newDetalhe = InventarioDetalhe::create([
            'inventario_id' => $inventario->id,
            'id_bem' => $barcode,
            'status_leitura' => 'novo_sistema',
            'tratativa' => 'cadastrar', // Default for new found items
            'observacao' => $msg,
            'user_id_conferencia' => Auth::id(),
            'timestamp_leitura' => now()
        ]);

        return response()->json([
            'status' => 'info',
            'message' => $msg,
            'detalhe' => $newDetalhe->load('bem'),
            'is_cross_church' => !!$churchWarning
        ]);
    }
}
