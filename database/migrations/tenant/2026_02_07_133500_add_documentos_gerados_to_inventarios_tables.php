<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('tenant')->table('inventarios', function (Blueprint $table) {
            $table->json('documentos_gerados')->nullable()->after('responsavel');
        });

        Schema::connection('tenant')->table('inventario_detalhes', function (Blueprint $table) {
            $table->json('documentos_gerados')->nullable()->after('documento_doacao_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->table('inventarios', function (Blueprint $table) {
            $table->dropColumn('documentos_gerados');
        });

        Schema::connection('tenant')->table('inventario_detalhes', function (Blueprint $table) {
            $table->dropColumn('documentos_gerados');
        });
    }
};
