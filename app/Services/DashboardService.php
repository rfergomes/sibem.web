<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get statistics for the Operator profile (Current Year Only).
     */
    public function getStatsForOperator(User $user)
    {
        // Operator sees only current year
        return $this->getStatsForLocalAdmin($user, ['ano' => Carbon::now()->year]);
    }

    /**
     * Get statistics for the Local Admin profile (Allows filters).
     */
    public function getStatsForLocalAdmin(User $user, $filters = [])
    {
        $year = $filters['ano'] ?? Carbon::now()->year;
        $sectorId = $filters['setor_id'] ?? null;

        // 1. Total Active Churches (Target)
        // Adjust query for Sector filter if provided
        $igrejasQuery = DB::connection('mysql')->table('igrejas_global')
            ->where('id_status', 1);

        if ($user->local_id) {
            $igrejasQuery->where('local_id', $user->local_id);
        }

        if ($sectorId) {
            $sectorName = DB::connection('mysql')->table('setores')->where('id', $sectorId)->value('nome');
            if ($sectorName) {
                $igrejasQuery->where('setor', $sectorName);
            }
        }

        // Count total churches in scope (Target)
        $totalIgrejas = $igrejasQuery->count();

        // Get relevant Church IDs to filter Inventories
        $churchIds = $igrejasQuery->pluck('id')->toArray();

        // 2. Realized Inventories (In selected year, for selected churches)
        $inventariosQuery = DB::connection('tenant')->table('inventarios')
            ->where('ano', $year)
            ->whereIn('status', ['fechado', 'auditado'])
            ->whereIn('id_igreja', $churchIds); // Ensure we only count inventories from filtered churches

        $inventariosRealizados = $inventariosQuery->count();

        // 3. Progress
        $progresso = $totalIgrejas > 0 ? round(($inventariosRealizados / $totalIgrejas) * 100) : 0;

        // 4. Inactive Churches (Unfiltered by sector? Probably should respect filter)
        $igrejasInativasQuery = DB::connection('mysql')->table('igrejas_global')
            ->where('id_status', '!=', 1);

        if ($user->local_id) {
            $igrejasInativasQuery->where('local_id', $user->local_id);
        }

        if ($sectorId && isset($sectorName)) {
            $igrejasInativasQuery->where('setor', $sectorName);
        }
        $igrejasInativas = $igrejasInativasQuery->count();

        // 5. Inventories by Sector (Chart) - Pie/Donut
        // If Sector filter is ON, this chart will show 100% for that sector?
        // Yes, but let's query all sectors if no filter is applied.

        $statsPorSetor = [];

        // Build chart data based on filtered churches but grouped by sector
        // Fetch distributions
        $realizedByChurch = $inventariosQuery->pluck('id_igreja')->toArray();
        $churchesWithSector = DB::connection('mysql')->table('igrejas_global')
            ->whereIn('id', $churchIds) // Respect filter
            ->pluck('setor', 'id'); // Pluck NAME directy

        foreach ($realizedByChurch as $churchId) {
            $sName = $churchesWithSector[$churchId] ?? null;
            if ($sName) {
                if (!isset($statsPorSetor[$sName])) {
                    $statsPorSetor[$sName] = 0;
                }
                $statsPorSetor[$sName]++;
            }
        }

        // 6. Inventories by Month (Chart) - Bar
        // Re-use query to group by month
        $byMonth = DB::connection('tenant')->table('inventarios')
            ->where('ano', $year)
            ->whereIn('status', ['fechado', 'auditado'])
            ->whereIn('id_igreja', $churchIds)
            ->select('mes', DB::raw('count(*) as total'))
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        // Fill 1-12
        $monthlyData = array_fill(1, 12, 0);
        foreach ($byMonth as $m => $val) {
            $monthlyData[$m] = $val;
        }

        // 7. Departamentos (Non-Temple properties)
        // Fetch 'TEMPLO' type ID
        $temploId = DB::connection('mysql')->table('tipos_imovel')
            ->where('nome', 'TEMPLO')
            ->value('id');

        $departamentos = 0;
        $igrejasAtivas = $totalIgrejas; // Default fallback

        if ($temploId) {
            // Recalculate based on type
            // Active Churches (Temples)
            $igrejasAtivasQuery = clone $igrejasQuery;
            $igrejasAtivas = $igrejasAtivasQuery->where('id_tipo', $temploId)->count();

            // Departments (Others)
            $deptosQuery = clone $igrejasQuery;
            // Remove previous id_tipo where valid? No, clone preserves.
            // We need a fresh query or modify clone.
            // Since $igrejasQuery was 'active=true' and 'setor filter'.
            // Accessing the base builder might be tricky if not careful.

            // Simpler: Just run separate counts for clarity
            $baseQuery = DB::connection('mysql')->table('igrejas_global')
                ->where('id_status', 1);

            if ($user->local_id) {
                $baseQuery->where('local_id', $user->local_id);
            }
            if ($sectorId && isset($sectorName))
                $baseQuery->where('setor', $sectorName);

            $igrejasAtivas = (clone $baseQuery)->where('id_tipo', $temploId)->count();
            $departamentos = (clone $baseQuery)->where('id_tipo', '!=', $temploId)->count();

            // Re-assign Total to be just Temples for "Meta" if user implies Meta = Temples?
            // "Previsão de inventário... 115 igrejas... Previsão de todas".
            // Does inventory apply to Departments?
            // "Inventário das casas de oração". Departments usually have inventory too.
            // I will keep 'meta_anual' as TOTAL (Temples + Depts) or just Temples?
            // User: "Admin Campinas possui 115 igrejas... 115 inventários".
            // Likely Meta = Temples + Depts (Inventory targets everything).
            // But usually "Igrejas" in cards means Temples.
            // I will separate metrics display but stick to Total for Meta.
        }

        return [
            'total_igrejas' => $igrejasAtivas, // Display as "Igrejas"
            'departamentos' => $departamentos,
            'igrejas_inativas' => $igrejasInativas,
            'inventarios_realizados' => $inventariosRealizados,
            'meta_anual' => $totalIgrejas, // All active units
            'pendentes' => $totalIgrejas - $inventariosRealizados,
            'progresso' => $progresso,
            'chart_setor' => $statsPorSetor,
            'chart_mensal' => array_values($monthlyData),
            'ano' => $year
        ];
    }

    public function getStatsForRegionalAdmin(User $user)
    {
        // 1. Determine Scope (Locals)
        $locaisQuery = \App\Models\Local::where('active', true);

        if ($user->perfil_id == 2 && $user->regional_id) { // Regional Admin
            $locaisQuery->where('regional_id', $user->regional_id);
        }
        // System Admin (perfil_id 1) sees all active locals (no extra filter needed)

        $locais = $locaisQuery->get();
        $localIds = $locais->pluck('id')->toArray();

        // 2. Global Stats (Fast - Single Query active 'mysql')
        // Total Active Churches (Templos)
        $temploId = DB::connection('mysql')->table('tipos_imovel')
            ->where('nome', 'TEMPLO')
            ->value('id');

        $baseQuery = DB::connection('mysql')->table('igrejas_global')
            ->whereIn('local_id', $localIds)
            ->where('id_status', 1);

        $totalIgrejas = (clone $baseQuery)->where('id_tipo', $temploId)->count();
        $departamentos = (clone $baseQuery)->where('id_tipo', '!=', $temploId)->count();

        $igrejasInativas = DB::connection('mysql')->table('igrejas_global')
            ->whereIn('local_id', $localIds)
            ->where('id_status', '!=', 1)
            ->count();

        // 3. Tenant Stats (Slow - Loop required)
        // Cache this part for performance (e.g., 10 minutes)
        $cacheKey = 'dashboard_stats_regional_' . $user->id;

        $tenantStats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($locais) {
            $realized = 0;
            $currentYear = Carbon::now()->year;

            foreach ($locais as $local) {
                // Dynamically connect to tenant
                if (empty($local->db_host) || empty($local->db_name))
                    continue;

                try {
                    // Set config
                    config([
                        'database.connections.tenant_temp.driver' => 'mysql',
                        'database.connections.tenant_temp.host' => $local->db_host,
                        'database.connections.tenant_temp.database' => $local->db_name,
                        'database.connections.tenant_temp.username' => $local->db_user,
                        'database.connections.tenant_temp.password' => $local->db_password,
                        'database.connections.tenant_temp.charset' => 'utf8mb4',
                        'database.connections.tenant_temp.collation' => 'utf8mb4_unicode_ci',
                        'database.connections.tenant_temp.prefix' => '',
                    ]);

                    // Purge and Reconnect
                    DB::purge('tenant_temp');

                    $count = DB::connection('tenant_temp')->table('inventarios')
                        ->where('ano', $currentYear)
                        ->whereIn('status', ['fechado', 'auditado'])
                        ->count();

                    $realized += $count;

                } catch (\Exception $e) {
                    // Silent fail for one tenant, log it
                    \Illuminate\Support\Facades\Log::warning("Dashboard Regional: Failed to connect to local {$local->id}: " . $e->getMessage());
                    continue;
                }
            }

            return ['realized' => $realized];
        });

        $inventariosRealizados = $tenantStats['realized'];
        $progresso = $totalIgrejas > 0 ? round(($inventariosRealizados / $totalIgrejas) * 100) : 0;

        return [
            'total_igrejas' => $totalIgrejas,
            'departamentos' => $departamentos,
            'igrejas_inativas' => $igrejasInativas,
            'inventarios_realizados' => $inventariosRealizados,
            'meta_anual' => $totalIgrejas,
            'pendentes' => $totalIgrejas - $inventariosRealizados,
            'progresso' => $progresso,
            'chart_setor' => [], // Not implemented for macro view yet (too scattered)
            'chart_mensal' => [], // Not implemented for macro view yet
            'ano' => Carbon::now()->year
        ];
    }
}
