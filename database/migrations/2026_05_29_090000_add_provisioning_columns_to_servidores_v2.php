<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = 'mysql_sys';

        // Fetch columns using a simple SHOW COLUMNS statement
        $cols = DB::connection($connection)->select("SHOW COLUMNS FROM servidores_v2");
        $existingCols = collect($cols)->pluck('Field')->toArray();

        if (!in_array('provisionado', $existingCols)) {
            DB::connection($connection)->statement("ALTER TABLE servidores_v2 ADD COLUMN provisionado TINYINT(1) DEFAULT 0 AFTER ativo");
        }
        if (!in_array('data_provisionamento', $existingCols)) {
            DB::connection($connection)->statement("ALTER TABLE servidores_v2 ADD COLUMN data_provisionamento TIMESTAMP NULL DEFAULT NULL AFTER provisionado");
        }
        
        try {
            DB::connection($connection)->statement("ALTER TABLE servidores_v2 ADD INDEX idx_provisionado (provisionado)");
        } catch (\Exception $e) {
            // Index might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = 'mysql_sys';
        
        $cols = DB::connection($connection)->select("SHOW COLUMNS FROM servidores_v2");
        $existingCols = collect($cols)->pluck('Field')->toArray();

        if (in_array('provisionado', $existingCols)) {
            DB::connection($connection)->statement("ALTER TABLE servidores_v2 DROP COLUMN provisionado");
        }
        if (in_array('data_provisionamento', $existingCols)) {
            DB::connection($connection)->statement("ALTER TABLE servidores_v2 DROP COLUMN data_provisionamento");
        }
    }
};
