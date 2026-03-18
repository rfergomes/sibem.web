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
        Schema::create('status_bens', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Ativo, Baixado, etc.
            $table->boolean('contabiliza')->default(true); // Se conta no inventário
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_bens');
    }
};
