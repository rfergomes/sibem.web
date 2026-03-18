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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // RBAC Columns
            // RBAC Columns
            // $table->enum('perfil', [...]); // REPLACED BY PERFIL_ID
            $table->unsignedBigInteger('perfil_id')->nullable();

            // Scope Columns
            $table->unsignedBigInteger('regional_id')->nullable(); // Removed constrained() to avoid migration order error
            $table->unsignedBigInteger('local_id')->nullable();

            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
