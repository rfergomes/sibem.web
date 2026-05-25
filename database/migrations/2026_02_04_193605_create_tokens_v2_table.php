<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tokens_v2')) {
            Schema::create('tokens_v2', function (Blueprint $table) {
                $table->id();
                $table->string('token')->unique();
                $table->string('identificador_maquina');
                $table->unsignedBigInteger('local_id');
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do not drop existing tables
    }
};
