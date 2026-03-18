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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Localidade e Criador
            $table->foreignId('local_id')->constrained('locais')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Dados do Responsável Local/Ministro
            $table->string('responsavel_nome');
            $table->string('responsavel_cargo')->nullable();
            $table->string('responsavel_contato')->nullable(); // Celular/WhatsApp

            // Detalhes do Agendamento
            $table->dateTime('scheduled_at');
            $table->string('status')->default('previsao'); // previsao, confirmado, cancelado, adiado
            $table->text('notes')->nullable();
            $table->text('justification')->nullable(); // Para cancelamentos ou adiamentos

            // Auditoria da última ação
            $table->foreignId('action_user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
