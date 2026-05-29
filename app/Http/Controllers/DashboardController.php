<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Regional;
use App\Models\Local;
use App\Models\Igreja;
use App\Models\User;
use App\Models\Inventario;
use App\Models\Setor;
use App\Models\TokenV2;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeLocalId = session()->get('active_admlc_id') ?? $user->local_id;
        $activeLocal = Local::with('regional')->find($activeLocalId);

        // Fetch upcoming schedules (next 7 days) to notify the user once per session
        if (!session()->has('agendamentos_notificados')) {
            $today = date('Y-m-d');
            $sevenDaysLater = date('Y-m-d', strtotime('+7 days'));

            $upcomingQuery = \App\Models\Agendamento::with('igreja')
                ->whereIn('status', ['Confirmado', 'Reagendado', 'Pendente'])
                ->whereBetween('data', [$today, $sevenDaysLater]);

            if ($user->isAdminSistema()) {
                // system admin sees all
            } elseif ($user->isAdminRegional()) {
                $regionalId = $user->regional_id;
                $upcomingQuery->whereHas('local', function($q) use ($regionalId) {
                    $q->where('admrg_id', $regionalId);
                });
            } else {
                $upcomingQuery->where('admlc_id', $activeLocalId);
            }

            $upcomingList = $upcomingQuery->orderBy('data', 'asc')->orderBy('horario', 'asc')->get()
                ->map(function($a) {
                    return [
                        'igreja' => $a->igreja ? $a->igreja->igreja : 'Não identificada',
                        'data' => date('d/m/Y', strtotime($a->data)),
                        'horario' => substr($a->horario, 0, 5),
                        'responsavel' => $a->responsavel_nome,
                        'status' => $a->status
                    ];
                })->toArray();
            
            if (!empty($upcomingList)) {
                session()->put('upcoming_schedules', $upcomingList);
            }
            session()->put('agendamentos_notificados', true);
        }

        $selectedYear = $request->input('ano', date('Y'));
        $anosDisponiveis = collect(range(date('Y') - 3, date('Y') + 1))->reverse()->values();

        // Check if viewing in Regional Dashboard mode
        $selectedRegionalId = null;
        $availableRegionais = collect();
        
        if ($user->isAdminSistema()) {
            $availableRegionais = Regional::orderBy('adm_regional')->get();
            if ($request->has('selected_regional_id')) {
                $selectedRegionalId = $request->input('selected_regional_id');
                if ($selectedRegionalId) {
                    session()->put('selected_regional_id', $selectedRegionalId);
                } else {
                    session()->forget('selected_regional_id');
                }
            } else {
                $selectedRegionalId = session()->get('selected_regional_id');
            }
        } elseif ($user->isAdminRegional()) {
            $selectedRegionalId = $user->regional_id;
        }

        // --- REGIONAL DASHBOARD MODE ---
        if ($selectedRegionalId) {
            $regional = Regional::find($selectedRegionalId);
            $locais = Local::where('admrg_id', $selectedRegionalId)->withCount('igrejas')->orderBy('adm_local')->get();

            $regLocaisStats = [];
            $totalIgrejas = 0;
            $totalInventariosConcluidos = 0;
            $totalInventariosPendentes = 0;

            foreach ($locais as $local) {
                $concluidos = 0;
                $pendentes = 0;
                $statusConexao = 'sem_servidor';

                $server = \Illuminate\Support\Facades\DB::connection('mysql_sys')
                    ->table('servidores_v2')
                    ->where('admlc_id', $local->admlc_id)
                    ->where('ativo', 1)
                    ->first();

                if ($server && $server->banco) {
                    $connectionName = 'tenant_temp_' . $local->admlc_id;
                    config([
                        "database.connections.{$connectionName}" => [
                            'driver' => 'mysql',
                            'host' => $server->servidor,
                            'port' => $server->porta ?? '3306',
                            'database' => $server->banco,
                            'username' => $server->usuario,
                            'password' => $server->senha ?? '',
                            'charset' => 'utf8mb4',
                            'collation' => 'utf8mb4_unicode_ci',
                            'prefix' => '',
                            'strict' => true,
                            'options' => [
                                \PDO::ATTR_TIMEOUT => 2, // 2-second timeout
                            ],
                        ]
                    ]);

                    try {
                        $concluidos = \Illuminate\Support\Facades\DB::connection($connectionName)
                            ->table('inventarios_v2')
                            ->whereRaw('YEAR(data) = ?', [$selectedYear])
                            ->whereIn('situacao', ['Finalizado', 'Concluído', 'Auditado'])
                            ->count();

                        $statusConexao = 'online';
                    } catch (\Exception $e) {
                        logger()->error("Erro ao conectar no banco local da adm {$local->admlc_id}: " . $e->getMessage());
                        $statusConexao = 'offline';
                    } finally {
                        \Illuminate\Support\Facades\DB::purge($connectionName);
                    }
                }

                $pendentes = max(0, $local->igrejas_count - $concluidos);

                $totalIgrejas += $local->igrejas_count;
                $totalInventariosConcluidos += $concluidos;
                $totalInventariosPendentes += $pendentes;

                $progresso = $local->igrejas_count > 0 ? min(100, round(($concluidos / $local->igrejas_count) * 100, 1)) : 0;

                $regLocaisStats[] = (object)[
                    'admlc_id' => $local->admlc_id,
                    'adm_local' => $local->adm_local,
                    'razao_social' => $local->razao_social,
                    'cidade' => $local->cidade,
                    'uf' => $local->uf,
                    'igrejas_count' => $local->igrejas_count,
                    'inventarios_concluidos' => $concluidos,
                    'inventarios_pendentes' => $pendentes,
                    'progresso' => $progresso,
                    'status_conexao' => $statusConexao
                ];
            }

            $stats = [
                'locais' => $locais->count(),
                'igrejas' => $totalIgrejas,
                'inventarios_concluidos' => $totalInventariosConcluidos,
                'inventarios_abertos' => $totalInventariosPendentes,
            ];

            return view('dashboard', compact(
                'stats',
                'regional',
                'regLocaisStats',
                'selectedYear',
                'anosDisponiveis',
                'selectedRegionalId',
                'availableRegionais'
            ));
        }

        // --- LOCAL/STANDARD DASHBOARD MODE ---
        $stats = [
            'regionais' => 0,
            'locais' => 0,
            'igrejas' => 0,
            'usuarios' => 0,
            'inventarios_abertos' => 0,
            'inventarios_concluidos' => 0,
        ];

        if ($user->isAdminSistema()) {
            $stats['regionais'] = Regional::count();
            $stats['locais'] = Local::count();
            $stats['igrejas'] = Igreja::count();
            $stats['usuarios'] = User::count();
        } else {
            $stats['igrejas'] = Igreja::where('admlc_id', $activeLocalId)->count();
            $stats['usuarios'] = User::where('admlc_id', $activeLocalId)->count();
        }

        $setoresStats = collect();
        if ($activeLocal) {
            try {
                 $stats['inventarios_concluidos'] = Inventario::whereRaw('YEAR(data) = ?', [$selectedYear])
                    ->whereIn('situacao', ['Finalizado', 'Concluído', 'Auditado'])
                    ->count();

                $stats['inventarios_abertos'] = max(0, $stats['igrejas'] - $stats['inventarios_concluidos']);

                // Build sector breakdown in memory safely
                $igrejas = Igreja::where('admlc_id', $activeLocalId)->get();
                $setores = Setor::where('admlc_id', $activeLocalId)->orderBy('cod_setor')->get();
                $inventarios = Inventario::whereRaw('YEAR(data) = ?', [$selectedYear])
                    ->select('igreja_id', 'situacao')
                    ->get();
                $inventariosMap = collect($inventarios)->keyBy('igreja_id');

                $setoresStats = $setores->map(function($setor) use ($igrejas, $inventariosMap) {
                    $igrejasNoSetor = $igrejas->where('cod_setor', $setor->cod_setor);
                    $concluidos = 0;

                    foreach ($igrejasNoSetor as $ig) {
                        $inv = $inventariosMap->get($ig->igreja_id);
                        if ($inv) {
                            if (in_array($inv->situacao, ['Finalizado', 'Concluído', 'Auditado'])) {
                                $concluidos++;
                            }
                        }
                    }

                    $pendentes = max(0, $igrejasNoSetor->count() - $concluidos);
                    $progresso = $igrejasNoSetor->count() > 0 ? min(100, round(($concluidos / $igrejasNoSetor->count()) * 100, 1)) : 0;

                    return (object)[
                        'cod_setor' => $setor->cod_setor,
                        'descricao' => $setor->descricao,
                        'igrejas_count' => $igrejasNoSetor->count(),
                        'inventarios_concluidos' => $concluidos,
                        'inventarios_pendentes' => $pendentes,
                        'progresso' => $progresso
                    ];
                });
            } catch (\Exception $e) {
                logger()->error("Tenant connection error in dashboard local view for active local {$activeLocalId}: " . $e->getMessage());
            }
        }

        // Recent Token Requests
        if ($user->isAdminSistema()) {
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
            'setoresStats',
            'recentTokenRequests',
            'selectedYear',
            'anosDisponiveis',
            'selectedRegionalId',
            'availableRegionais'
        ));
    }
}
