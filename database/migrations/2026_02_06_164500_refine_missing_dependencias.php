<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $dependencias = [
            133 => 'DORMITÓRIO',
            135 => 'ESPAÇO INFANTIL (antigo)',
            514 => 'ESPAÇO BÍBLICO INFANTIL (novo)',
        ];

        foreach ($dependencias as $id => $nome) {
            DB::table('dependencias')->updateOrInsert(
                ['id' => $id],
                ['nome' => $nome, 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
