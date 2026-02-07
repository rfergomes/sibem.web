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
        Schema::create('dependencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setor_id');
            $table->string('nome'); // Sala 1, Cozinha, etc.
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('setor_id')->references('id')->on('setores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependencias');
    }
};
