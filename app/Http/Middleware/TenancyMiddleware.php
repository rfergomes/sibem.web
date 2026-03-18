<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TenancyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Skip for logout route
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        // 2. System admins (perfil_id=1) bypass tenant connection — they use the main DB
        if (auth()->check() && auth()->user()->perfil_id == 1) {
            return $next($request);
        }

        // 3. Check if we have a Tenant ID in session
        if (Session::has('current_tenant_id')) {
            $tenantData = Session::get('current_tenant_connection_data');

            if ($tenantData && !empty($tenantData->db_name) && !empty($tenantData->db_host)) {
                // 4. Configure the 'tenant' connection dynamically
                Config::set('database.connections.tenant.host', $tenantData->db_host);
                Config::set('database.connections.tenant.database', $tenantData->db_name);
                Config::set('database.connections.tenant.username', $tenantData->db_user);

                if (!empty($tenantData->db_password)) {
                    Config::set('database.connections.tenant.password', $tenantData->db_password);
                } else {
                    Config::set('database.connections.tenant.password', '');
                }

                try {
                    DB::purge('tenant');
                    DB::reconnect('tenant');
                    DB::connection('tenant')->getPdo();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Tenant Connection Error: " . $e->getMessage());
                    return response()->view('errors.tenant_connection', [], 500);
                }
            }
        }

        return $next($request);
    }
}
