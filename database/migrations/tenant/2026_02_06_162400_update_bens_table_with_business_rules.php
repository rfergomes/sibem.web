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
        Schema::table('bens', function (Blueprint $table) {
            // New Classification Relationship
            $table->unsignedBigInteger('id_tipo_bem')->nullable()->after('id_dependencia');

            // Business Rule Fields
            $table->boolean('is_lote')->default(false)->after('id_tipo_bem');
            $table->integer('quantidade')->default(1)->after('is_lote');
            $table->boolean('is_terceiro')->default(false)->after('quantidade');

            // If there's an existing id_conta_contabil, we'll keep it or move it to normalization later.
            // But based on the plan, we infer it from tipo_bem.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bens', function (Blueprint $table) {
            $table->dropColumn(['id_tipo_bem', 'is_lote', 'quantidade', 'is_terceiro']);
        });
    }
};
