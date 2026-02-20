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
        Schema::table('igrejas_global', function (Blueprint $table) {
            // 1. Shift IDs from 0-based to 1-based (Laravel standard)
            // Only if data exists and hasn't been shifted (safe check difficult here, assuming running once)
            DB::statement('UPDATE igrejas_global SET id_tipo = id_tipo + 1');

            // 2. Change column type to match types_imovel.id (bigint unsigned)
            $table->unsignedBigInteger('id_tipo')->change();

            // 3. Add Foreign Key
            $table->foreign('id_tipo')->references('id')->on('tipos_imovel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('igrejas_global', function (Blueprint $table) {
            $table->dropForeign(['id_tipo']);
            // Revert type (assuming originally int)
            $table->integer('id_tipo')->change();
            // Revert ID shift
            DB::statement('UPDATE igrejas_global SET id_tipo = id_tipo - 1');
        });
    }
};
