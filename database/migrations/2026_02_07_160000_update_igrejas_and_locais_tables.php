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
            $table->string('razao_social')->nullable();
            $table->string('cnpj', 20)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('uf', 2)->nullable();
            $table->text('observacao')->nullable();
            $table->integer('id_status')->nullable();
            $table->integer('id_tipo')->nullable();
        });

        Schema::table('locais', function (Blueprint $table) {
            $table->string('razao_social')->nullable();
            $table->string('cnpj', 20)->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->integer('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('igrejas_global', function (Blueprint $table) {
            $table->dropColumn([
                'razao_social',
                'cnpj',
                'logradouro',
                'numero',
                'uf',
                'observacao',
                'id_status',
                'id_tipo'
            ]);
        });

        Schema::table('locais', function (Blueprint $table) {
            $table->dropColumn([
                'razao_social',
                'cnpj',
                'cidade',
                'uf',
                'status'
            ]);
        });
    }
};
