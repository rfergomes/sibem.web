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
        Schema::create('bens', function (Blueprint $table) {
            $table->string('id_bem', 12)->primary(); // Barcode
            $table->text('descricao');
            $table->string('id_igreja')->index(); // Matches global church ID but treating as string for flexibility from old system
            $table->integer('id_dependencia')->nullable();
            $table->tinyInteger('id_status')->default(1); // 0: Inactive, 1: Active
            $table->enum('origem', ['importado', 'manual'])->default('importado');
            $table->timestamp('data_importacao')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bens');
    }
};
