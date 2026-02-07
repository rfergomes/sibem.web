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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->integer('ano');
            $table->integer('mes');
            $table->string('codigo_unico')->unique(); // id_igreja + sequence
            $table->string('id_igreja');
            $table->enum('status', ['aberto', 'fechado', 'auditado'])->default('aberto');
            $table->unsignedBigInteger('user_id_abertura');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
