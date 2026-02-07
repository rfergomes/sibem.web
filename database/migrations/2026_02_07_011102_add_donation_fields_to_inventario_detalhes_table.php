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
        Schema::connection('tenant')->table('inventario_detalhes', function (Blueprint $table) {
            $table->boolean('is_doacao')->default(false)->after('observacao');
            $table->string('documento_doacao_path', 500)->nullable()->after('is_doacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->table('inventario_detalhes', function (Blueprint $table) {
            $table->dropColumn(['is_doacao', 'documento_doacao_path']);
        });
    }
};
