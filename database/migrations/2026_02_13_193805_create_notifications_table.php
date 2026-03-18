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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Destinatário da notificação
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Local relacionado (opcional)
            $table->foreignId('local_id')->nullable()->constrained('locais')->onDelete('cascade');

            // Tipo de notificação
            $table->string('type', 50); // 'access_request', 'inventory_open', etc.

            // Conteúdo da notificação
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable(); // URL para o recurso

            // Relacionamento polimórfico com o recurso original
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_type')->nullable(); // 'App\Models\Inventory', etc.

            // Controle de leitura
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Indexes para performance
            $table->index(['user_id', 'read_at']); // Queries de não lidas
            $table->index('local_id'); // Filtrar por local
            $table->index('created_at'); // Ordenação
            $table->index(['user_id', 'local_id', 'read_at']); // Queries complexas
            $table->index(['related_id', 'related_type']); // Buscar por recurso
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
