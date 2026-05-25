<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Regional;
use App\Models\Local;
use App\Models\Igreja;
use App\Models\User;
use App\Models\Inventario;
use App\Models\TokenV2;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeLocalId = session()->get('active_admlc_id') ?? $user->local_id;

        // Active Local and Regional Info
        $activeLocal = Local::with('regional')->find($activeLocalId);
        
        // Dynamic Tenant Selection options for the UI
        $availableLocais = collect();
        $availableRegionais = collect();

        if ($user->isAdminSistema()) {
            $availableRegionais = Regional::orderBy('adm_regional')->get();
            $activeRegionalId = request('selected_regional_id', session()->get('selected_regional_id'));
            if ($activeRegionalId) {
                $availableLocais = Local::where('admrg_id', $activeRegionalId)->orderBy('adm_local')->get();
            } else {
                $availableLocais = Local::orderBy('adm_local')->get();
            }
        } elseif ($user->isAdminRegional()) {
            $availableLocais = Local::where('admrg_id', $user->regional_id)->orderBy('adm_local')->get();
        }

        // --- STATS COMPILATION (Scoped by Role) ---
        $stats = [
            'regionais' => 0,
            'locais' => 0,
            'igrejas' => 0,
            'usuarios' => 0,
            'inventarios_abertos' => 0,
            'inventarios_concluidos' => 0,
        ];

        // 1. Regionals Count
        if ($user->isAdminSistema()) {
            $stats['regionais'] = Regional::count();
        }

        // 2. Locals Count
        if ($user->isAdminSistema()) {
            $stats['locais'] = Local::count();
        } elseif ($user->isAdminRegional()) {
            $stats['locais'] = Local::where('admrg_id', $user->regional_id)->count();
        }

        // 3. Churches Count
        $igrejasQuery = Igreja::query();
        if ($user->isAdminSistema()) {
            // All
        } elseif ($user->isAdminRegional()) {
            $localIds = Local::where('admrg_id', $user->regional_id)->pluck('id');
            $igrejasQuery->whereIn('admlc_id', $localIds);
        } else {
            $igrejasQuery->where('admlc_id', $activeLocalId);
        }
        $stats['igrejas'] = $igrejasQuery->count();

        // 4. Users Count
        $usersQuery = User::query();
        if ($user->isAdminSistema()) {
            // All
        } elseif ($user->isAdminRegional()) {
            $localIds = Local::where('admrg_id', $user->regional_id)->pluck('id');
            $usersQuery->whereIn('admlc_id', $localIds);
        } else {
            $usersQuery->where('admlc_id', $activeLocalId);
        }
        $stats['usuarios'] = $usersQuery->count();

        // 5. Inventories Count (Dynamic Tenant Connection)
        if ($activeLocal) {
            try {
                $stats['inventarios_abertos'] = Inventario::where('situacao', 'Pendente')->count();
                $stats['inventarios_concluidos'] = Inventario::whereIn('situacao', ['Finalizado', 'Concluído', 'Auditado'])->count();
            } catch (\Exception $e) {
                // Connection failed or tables do not exist for this local
                logger()->error("Tenant connection error for local ID {$activeLocalId}: " . $e->getMessage());
            }
        }

        // Recent Token Requests
        if ($user->isAdminSistema() || $user->isAdminRegional()) {
            $recentTokenRequests = TokenV2::with(['user', 'local'])
                ->where('ativo', 0)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            $recentTokenRequests = collect();
        }

        return view('dashboard', compact(
            'stats',
            'activeLocal',
            'availableLocais',
            'availableRegionais',
            'recentTokenRequests'
        ));
    }
}
