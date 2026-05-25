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
                $local = Local::find($activeLocalId);
                if ($local && $local->db_name) {
                    config([
                        'database.connections.tenant.host' => $local->db_host,
                        'database.connections.tenant.database' => $local->db_name,
                        'database.connections.tenant.username' => $local->db_user,
                        'database.connections.tenant.password' => $local->db_password ?? '',
                    ]);

                    DB::purge('tenant');
                }
            }
        }

        return $next($request);
    }
}
