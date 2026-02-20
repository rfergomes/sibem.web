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
        Schema::create('tipos_imovel', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->timestamps();
        });

        // Insert default types
        $tipos = [
            'NÃO DEFINIDO',
            'ALMOXARIFADO',
            'ALOJAMENTO',
            'ANEXO',
            'ANEXO ADMINISTRATIVO',
            'ANEXO PIEDADE',
            'BARRACAO ADMINISTRAÇÃO',
            'BARRACÃO PIEDADE',
            'CASA DE APOIO',
            'COMPLEXO ADMINISTRATIVO',
            'DEPENDÊNCIA',
            'DEPENDÊNCIA ADMINISTRATIVA',
            'DEPENDÊNCIA PIEDADE',
            'ESPAÇO INFANTIL',
            'ESTACIONAMENTO',
            'TEMPLO',
            'TERRENO'
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_imovel')->insert([
                'nome' => $tipo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_imovel');
    }
};
