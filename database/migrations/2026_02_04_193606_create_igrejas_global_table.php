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
        Schema::create('igrejas_global', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_id')->constrained('locais');
            $table->string('codigo_ccb')->nullable();
            $table->string('nome');
            $table->string('cidade')->nullable();
            $table->string('bairro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('igrejas_global');
    }
};
