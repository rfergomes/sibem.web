<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario;
use App\Models\Igreja;
use App\Models\Local;

class InventarioController extends Controller
{
    public function concluidos(Request $request)
    {
        $user = Auth::user();
        $activeLocalId = session()->get('active_admlc_id') ?? $user->local_id;

        // Fetch local info
        $local = Local::find($activeLocalId);

        // Fetch filter options based on the active local
        // Using dynamic connection 'tenant'
        $anos = [];
        $setores = [];
        $igrejas = [];
        $inventarios = collect();
        $chartValues = array_fill(0, 12, 0);
        $selectedYear = $request->input('ano');
        if (!$request->has('ano')) {
            $selectedYear = date('Y');
        }

        if ($local) {
            try {
                $anos = \Illuminate\Support\Facades\DB::connection('tenant')
                    ->table('inventarios_v2')
                    ->selectRaw('YEAR(data) as ano')
                    ->distinct()
                    ->whereNotNull('data')
                    ->orderBy('ano', 'desc')
                    ->pluck('ano');
                
                $setores = \Illuminate\Support\Facades\DB::connection('mysql_sys')
                    ->table('igrejas_v2')
                    ->where('admlc_id', $activeLocalId)
                    ->whereNotNull('cod_setor')
                    ->where('cod_setor', '<>', '')
                    ->select('cod_setor as setor')
                    ->distinct()
                    ->orderBy('cod_setor')
                    ->pluck('setor');

                $igrejasQuery = Igreja::where('admlc_id', $activeLocalId)->orderBy('igreja');
                if ($request->filled('setor')) {
                    $igrejasQuery->where('cod_setor', $request->setor);
                }
                $igrejas = $igrejasQuery->get();

                // Build query
                $query = Inventario::with('igreja')->orderBy('data', 'desc');

                // Apply filters
                if ($request->has('ano')) {
                    if ($request->filled('ano')) {
                        $query->whereRaw('YEAR(data) = ?', [$request->ano]);
                    }
                } else {
                    $query->whereRaw('YEAR(data) = ?', [$selectedYear]);
                }

                if ($request->filled('setor')) {
                    $setor = $request->setor;
                    $igrejaIds = Igreja::where('admlc_id', $activeLocalId)
                        ->where('cod_setor', $setor)
                        ->pluck('igreja_id')
                        ->toArray();
                    $query->whereIn('igreja_id', $igrejaIds);
                }

                if ($request->filled('igreja_id')) {
                    $codigoCcb = $request->igreja_id;
                    $query->where('igreja_id', $codigoCcb);
                }

                $inventarios = $query->paginate(15)->withQueryString();

                // Chart Data (consolidated monthly completed inventories for selected/default year)
                // Using selectedYear set above
                
                $chartDataRaw = \Illuminate\Support\Facades\DB::connection('tenant')
                    ->table('inventarios_v2')
                    ->selectRaw('MONTH(data) as mes, COUNT(*) as total')
                    ->whereRaw('YEAR(data) = ?', [$selectedYear])
                    ->whereIn('situacao', ['Finalizado', 'Concluído', 'Auditado'])
                    ->groupByRaw('MONTH(data)')
                    ->orderByRaw('MONTH(data)')
                    ->pluck('total', 'mes')
                    ->toArray();

                // Initialize 12 months array
                $chartData = array_fill(1, 12, 0);
                foreach ($chartDataRaw as $mes => $total) {
                    $chartData[$mes] = $total;
                }
                $chartValues = array_values($chartData);
            } catch (\Exception $e) {
                // Handle case where tenant database connection fails or tables don't exist
                session()->flash('error', 'Falha ao conectar com o banco da Administração Local: ' . $e->getMessage());
            }
        }

        // Convert keys to Portuguese month names for labels
        $monthNames = [
            1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 5 => 'Mai', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
        ];
        
        $chartLabels = array_values($monthNames);

        return view('inventarios.concluidos', compact(
            'inventarios',
            'anos',
            'setores',
            'igrejas',
            'chartLabels',
            'chartValues',
            'selectedYear',
            'local'
        ));
    }
}
