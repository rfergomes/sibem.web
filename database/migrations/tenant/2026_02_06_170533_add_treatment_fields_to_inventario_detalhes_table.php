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
        Schema::table('inventario_detalhes', function (Blueprint $table) {
            $table->string('tratativa')->default('nenhuma')->after('status_leitura');
            $table->text('observacao')->nullable()->after('tratativa');
            $table->unsignedBigInteger('id_dependencia_original')->nullable()->after('observacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario_detalhes', function (Blueprint $table) {
            $table->dropColumn(['tratativa', 'observacao', 'id_dependencia_original']);
        });
    }
};
