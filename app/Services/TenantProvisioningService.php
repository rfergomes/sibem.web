<?php

namespace App\Services;

use App\Models\Local;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TenantProvisioningService
{
    /**
     * Provisions a new Local administration.
     *
     * @param Local $local
     * @return bool
     */
    public function provision(Local $local)
    {
        try {
            Log::info("Starting provisioning for Local: {$local->nome} (DB: {$local->db_name})");

            // 1. Create the database if it doesn't exist
            $this->createDatabase($local);

            // 2. Run migrations for the tenant
            $this->runMigrations($local);

            // 3. Seed default data
            $this->seedDefaultData($local);

            Log::info("Provisioning completed successfully for {$local->nome}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to provision Local {$local->nome}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Creates the tenant database.
     */
    private function createDatabase(Local $local)
    {
        // We use the default connection (mysql) to create the database
        // Use a safe query to avoid SQL injection even if db_name is from trusted source
        $dbName = $local->db_name;

        // Caution: Pre-creating DBs might require higher privileges.
        // If it fails, assume it already exists or must be created manually.
        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            Log::info("Database `{$dbName}` checked/created.");
        } catch (\Exception $e) {
            Log::warning("Could not create database `{$dbName}`: " . $e->getMessage() . ". Attempting to proceed assuming it exists.");
        }
    }

    /**
     * Runs tenant-specific migrations.
     */
    private function runMigrations(Local $local)
    {
        // Dynamically configure tenant connection for this process
        Config::set('database.connections.tenant.host', $local->db_host);
        Config::set('database.connections.tenant.database', $local->db_name);
        Config::set('database.connections.tenant.username', $local->db_user);
        Config::set('database.connections.tenant.password', $local->db_password);

        DB::purge('tenant');
        DB::reconnect('tenant');

        // Run migrations for the specific path
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        Log::info("Migrations finished for tenant: " . Artisan::output());
    }

    /**
     * Seeds initial data into the tenant database.
     */
    private function seedDefaultData(Local $local)
    {
        // Insert Status
        DB::connection('tenant')->table('status_bens')->updateOrInsert(
            ['id' => 1],
            ['nome' => 'Ativo', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::connection('tenant')->table('status_bens')->updateOrInsert(
            ['id' => 0],
            ['nome' => 'Inativo', 'created_at' => now(), 'updated_at' => now()]
        );

        // 1. Insert Default Sector for this Local (In Global Database DB)
        DB::table('setores')->updateOrInsert(
            ['local_id' => $local->id, 'nome' => 'ADM CENTRAL'],
            ['active' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        Log::info("Global sector seeded for local: {$local->id}");
    }
}
