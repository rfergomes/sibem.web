<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantConnectionService
{
    /**
     * Configura a conexão tenant em tempo de execução.
     * Usa mesmo host/port/user/pass do .env; apenas o database é trocado.
     */
    public function setTenantDatabase(string $databaseName): void
    {
        $default = config('database.connections.mysql');

        Config::set('database.connections.tenant.host', $default['host']);
        Config::set('database.connections.tenant.port', $default['port']);
        Config::set('database.connections.tenant.database', $databaseName);
        Config::set('database.connections.tenant.username', $default['username']);
        Config::set('database.connections.tenant.password', $default['password']);

        DB::purge('tenant');
    }

    /**
     * Configura tenant com credenciais completas (para futuro uso com tabela servidores).
     */
    public function setTenantConfig(
        string $host,
        string $port,
        string $database,
        string $username,
        string $password
    ): void {
        Config::set('database.connections.tenant.host', $host);
        Config::set('database.connections.tenant.port', $port);
        Config::set('database.connections.tenant.database', $database);
        Config::set('database.connections.tenant.username', $username);
        Config::set('database.connections.tenant.password', $password);

        DB::purge('tenant');
    }
}
