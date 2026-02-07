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
        // 1. Check if we have a Tenant ID in session
        if (Session::has('current_tenant_id')) {
            $tenantData = Session::get('current_tenant_connection_data');

            if ($tenantData) {
                // 2. Configure the 'tenant' connection dynamically
                Config::set('database.connections.tenant.host', $tenantData->db_host);
                Config::set('database.connections.tenant.database', $tenantData->db_name);
                Config::set('database.connections.tenant.username', $tenantData->db_user);

                // If there's a password (decrypted), set it. 
                // For dev/demo we stored empty strings or plain text, assume handling here:
                if (!empty($tenantData->db_password)) {
                    Config::set('database.connections.tenant.password', $tenantData->db_password);
                }

                // 3. Purge and Reconnect to apply changes
                DB::purge('tenant');
                DB::reconnect('tenant');
            }
        }

        return $next($request);
    }
}
