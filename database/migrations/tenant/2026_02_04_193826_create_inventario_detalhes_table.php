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
        Schema::create('inventario_detalhes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventarios')->onDelete('cascade');
            $table->string('id_bem', 12);
            $table->foreign('id_bem')->references('id_bem')->on('bens');

            $table->enum('status_leitura', ['encontrado', 'nao_encontrado', 'novo_sistema'])->default('nao_encontrado');
            $table->unsignedBigInteger('user_id_conferencia')->nullable();
            $table->timestamp('timestamp_leitura')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_detalhes');
    }
};
