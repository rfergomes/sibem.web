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
        $connection = 'mysql_sys';

        if (!Schema::connection($connection)->hasTable('agendamentos_v2')) {
            Schema::connection($connection)->create('agendamentos_v2', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admlc_id')->index();
                $table->unsignedBigInteger('igreja_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('responsavel_nome', 200);
                $table->string('responsavel_telefone', 30)->nullable();
                $table->string('acompanhante_nome', 200)->nullable();
                $table->date('data');
                $table->time('horario');
                $table->string('status', 30)->default('Pendente'); // Pendente, Confirmado, Reagendado, Cancelado
                $table->text('motivo_cancelamento')->nullable();
                $table->text('observacao')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = 'mysql_sys';
        Schema::connection($connection)->dropIfExists('agendamentos_v2');
    }
};
