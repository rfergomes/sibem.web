<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Local;

class DynamicTenancy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Get active local ID from session
            $activeLocalId = session()->get('active_admlc_id');

            // Fallback to user's local_id if not set or if user is Local Admin/Operator (cannot switch)
            if (!$activeLocalId || $user->isAdminLocal() || $user->isOperador()) {
                $activeLocalId = $user->local_id;
                session()->put('active_admlc_id', $activeLocalId);
            }

            if ($activeLocalId) {
                // Fetch connection parameters from servidores_v2 table in central db
                $server = DB::connection('mysql_sys')
                    ->table('servidores_v2')
                    ->where('admlc_id', $activeLocalId)
                    ->where('ativo', 1)
                    ->first();

                if ($server && $server->banco) {
                    config([
                        'database.connections.tenant.host' => $server->servidor,
                        'database.connections.tenant.port' => $server->porta ?? '3306',
                        'database.connections.tenant.database' => $server->banco,
                        'database.connections.tenant.username' => $server->usuario,
                        'database.connections.tenant.password' => $server->senha ?? '',
                    ]);

                    DB::purge('tenant');
                }
            }
        }

        return $next($request);
    }
}
