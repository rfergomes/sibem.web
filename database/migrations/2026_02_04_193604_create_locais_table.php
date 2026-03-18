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
        Schema::create('locais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regional_id')->constrained('regionais');
            $table->string('nome');

            // Database Connection Details for this Tenant
            $table->string('db_host')->default('127.0.0.1');
            $table->string('db_name');
            $table->string('db_user')->default('root');
            $table->string('db_password')->nullable();

            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locais');
    }
};
