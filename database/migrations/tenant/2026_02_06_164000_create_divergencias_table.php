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
        Schema::create('divergencias_inventario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventario_id');
            $table->string('id_bem')->nullable(); // Can be null if item found physically but not in system
            $table->string('codigo_divergencia', 2); // 01, 02, 03, 04
            $table->text('descricao')->nullable();
            $table->unsignedInteger('id_dependencia_anterior')->nullable();
            $table->unsignedInteger('id_dependencia_nova')->nullable();
            $table->string('registrado_por')->nullable(); // Brother responsible
            $table->timestamps();

            $table->foreign('inventario_id')->references('id')->on('inventarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divergencias_inventario');
    }
};
