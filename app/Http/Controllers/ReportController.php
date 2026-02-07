<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\InventarioDetalhe;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Generic method to generate and store PDF
     */
    private function generateAndStore($view, $data, $filename, $inventarioId, $folder = 'generated')
    {
        $pdf = Pdf::loadView($view, $data);

        $path = "reports/{$inventarioId}/{$folder}";
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }

        $fullPath = "{$path}/{$filename}";
        $pdf->save(storage_path("app/public/{$fullPath}"));

        return $fullPath;
    }

    /**
     * Formulário 14.3 – Declaração de saída de bens móveis
     * Used when: Item is removed/excluded (Tratativa: Excluir)
     */
    public function generate143($detalheId)
    {
        $detalhe = InventarioDetalhe::with(['inventario', 'bem'])->findOrFail($detalheId);
        $inventario = $detalhe->inventario;
        $bem = $detalhe->bem;

        $data = [
            'titulo' => 'DECLARAÇÃO DE SAÍDA DE BENS MÓVEIS',
            'form_code' => '14.3',
            'administracao' => 'CONGREGAÇÃO CRISTÃ NO BRASIL', // Should be dynamic based on config
            'cidade' => $bem->dependencia->local->nome ?? 'N/A', // Assuming logic
            'setor' => $inventario->inventory_code ?? 'Setor X',
            'data_emissao' => now()->format('d/m/Y'),
            'descricao_bem' => $bem->descricao,
            'motivo' => $detalhe->observacao ?? 'Descarte / Inservível',
            'responsavel' => $inventario->responsavel,
            'local_data' => now()->format('d/m/Y'),
        ];

        $filename = "form_14_3_{$detalhe->id}_" . time() . ".pdf";
        $path = $this->generateAndStore('pdf.formulario_14_3', $data, $filename, $inventario->id);

        $docs = $detalhe->documentos_gerados ?? [];
        $docs['form_14_3'] = $path;
        $detalhe->documentos_gerados = $docs;
        $detalhe->save();

        return response()->download(storage_path("app/public/{$path}"));
    }

    /**
     * Formulário 14.4 – Declaração de retirada de bem
     * Used when: Item is taken for repair or temporary usage elsewhere
     */
    public function generate144($detalheId)
    {
        $detalhe = InventarioDetalhe::with(['inventario', 'bem'])->findOrFail($detalheId);
        $data = [
            'titulo' => 'DECLARAÇÃO DE RETIRADA DE BEM',
            'form_code' => '14.4',
            'content' => 'Declaro que retirei o bem abaixo descrito para fins de manutenção/uso externo.',
            'bem' => $detalhe->bem->descricao,
            'data' => now()->format('d/m/Y'),
        ];

        // Using a generic template for now, or specific 14.4
        $filename = "form_14_4_{$detalhe->id}_" . time() . ".pdf";
        $path = $this->generateAndStore('pdf.formulario_14_4', $data, $filename, $detalhe->inventario_id);

        $docs = $detalhe->documentos_gerados ?? [];
        $docs['form_14_4'] = $path;
        $detalhe->documentos_gerados = $docs;
        $detalhe->save();

        return response()->download(storage_path("app/public/{$path}"));
    }

    /**
     * Formulário 14.5 – Ata de inventário de bens móveis
     * Consolidates the inventory results.
     */
    public function generate145($inventarioId)
    {
        $inventario = Inventario::with(['detalhes.bem'])->findOrFail($inventarioId);

        // Calculate stats
        $totalItems = $inventario->detalhes->count();
        $found = $inventario->detalhes->where('status_leitura', 'encontrado')->count();
        $missing = $inventario->detalhes->where('status_leitura', 'nao_encontrado')->count();
        $new = $inventario->detalhes->where('status_leitura', 'novo_sistema')->count();

        $data = [
            'titulo' => 'ATA DE INVENTÁRIO DE BENS MÓVEIS',
            'form_code' => '14.5',
            'inventario' => $inventario,
            'stats' => compact('totalItems', 'found', 'missing', 'new'),
            'data_emissao' => now()->format('d/m/Y'),
        ];

        $filename = "ata_14_5_{$inventario->id}_" . time() . ".pdf";
        $path = $this->generateAndStore('pdf.formulario_14_5', $data, $filename, $inventario->id);

        $docs = $inventario->documentos_gerados ?? [];
        $docs['form_14_5'] = $path;
        $inventario->documentos_gerados = $docs;
        $inventario->save();

        return response()->download(storage_path("app/public/{$path}"));
    }

    /**
     * Formulário 14.6 – Alteração de cadastro de bem
     * Used when: Item description or details are updated.
     */
    public function generate146($detalheId)
    {
        $detalhe = InventarioDetalhe::with(['inventario', 'bem'])->findOrFail($detalheId);

        $data = [
            'titulo' => 'ALTERAÇÃO DE CADASTRO DE BEM',
            'form_code' => '14.6',
            'bem_antigo' => 'Descrição Anterior (Historico)', // Ideally fetch from audit log if available
            'bem_novo' => $detalhe->bem->descricao,
            'motivo' => $detalhe->observacao,
            'data' => now()->format('d/m/Y'),
        ];

        $filename = "form_14_6_{$detalhe->id}_" . time() . ".pdf";
        $path = $this->generateAndStore('pdf.formulario_14_6', $data, $filename, $detalhe->inventario_id);

        $docs = $detalhe->documentos_gerados ?? [];
        $docs['form_14_6'] = $path;
        $detalhe->documentos_gerados = $docs;
        $detalhe->save();

        return response()->download(storage_path("app/public/{$path}"));
    }

    /**
     * Formulário 14.7 – Movimentação interna de bem
     * Used when: Item is moved between rooms/dependencies.
     */
    public function generate147($detalheId)
    {
        $detalhe = InventarioDetalhe::with(['inventario', 'bem'])->findOrFail($detalheId);
        // Logic to track from/to dependency is needed, usually found in Divergencia or logs
        // For now, using current dependency

        $data = [
            'titulo' => 'MOVIMENTAÇÃO INTERNA DE BEM',
            'form_code' => '14.7',
            'bem' => $detalhe->bem,
            'origem' => 'Dependencia Anterior', // Placeholder
            'destino' => $detalhe->bem->id_dependencia,
            'data' => now()->format('d/m/Y'),
        ];

        $filename = "form_14_7_{$detalhe->id}_" . time() . ".pdf";
        $path = $this->generateAndStore('pdf.formulario_14_7', $data, $filename, $detalhe->inventario_id);

        $docs = $detalhe->documentos_gerados ?? [];
        $docs['form_14_7'] = $path;
        $detalhe->documentos_gerados = $docs;
        $detalhe->save();

        return response()->download(storage_path("app/public/{$path}"));
    }

    /**
     * Formulário 14.8 – Movimento mensal de bem
     * Monthly report of all movements.
     */
    public function generate148($inventarioId)
    {
        $inventario = Inventario::with(['detalhes.bem'])->findOrFail($inventarioId);

        // Filter items that had movements
        // In this simplified logic, we take all 'novos' and 'encontrados' with divergence
        $items = $inventario->detalhes()
            ->where(function ($q) {
                $q->where('status_leitura', 'novo_sistema')
                    ->orWhere('observacao', 'like', '%Transferência%');
            })->get();

        $data = [
            'titulo' => 'MOVIMENTO MENSAL DE BEM',
            'form_code' => '14.8',
            'items' => $items,
            'mes' => $inventario->mes,
            'ano' => $inventario->ano,
            'data' => now()->format('d/m/Y'),
        ];

        $filename = "form_14_8_{$inventario->id}_" . time() . ".pdf";
        $path = $this->generateAndStore('pdf.formulario_14_8', $data, $filename, $inventario->id);

        $docs = $inventario->documentos_gerados ?? [];
        $docs['form_14_8'] = $path;
        $inventario->documentos_gerados = $docs;
        $inventario->save();

        return response()->download(storage_path("app/public/{$path}"));
    }
}
