<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Setores Table (Scoped by local_id)
        Schema::create('setores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_id');
            $table->string('nome');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('local_id')->references('id')->on('locais')->onDelete('cascade');
        });

        // 2. Create Dependencias Table (Shared Global List)
        Schema::create('dependencias', function (Blueprint $table) {
            $table->integer('id')->primary(); // Using user's specific IDs
            $table->string('nome', 250);
            $table->timestamp('data_alter')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 3. Seed Global Dependencias
        $dependencias = [
            0 => 'NÃO DEFINIDO',
            1 => 'PADRÃO',
            101 => 'EDIFICAÇÃO DO TEMPLO',
            102 => 'ALMOXARIFADO',
            103 => 'ATRIO',
            104 => 'ATRIO DIREITO',
            105 => 'ATRIO ESQUERDO',
            106 => 'CASA DO COMODATARIO',
            107 => 'COZINHA',
            108 => 'DISTRIBUIDORA',
            109 => 'FUNDO BIBLICO',
            110 => 'FRALDARIO',
            111 => 'QUINTAL',
            112 => 'GALERIA',
            113 => 'LAVANDERIA',
            114 => 'OFICINA DE ELETRONICA',
            115 => 'PORAO',
            116 => 'QUARTO DE BATISMO',
            117 => 'QUARTO DE LIMPEZA',
            118 => 'ESTACIONAMENTO',
            120 => 'SALA DA PIEDADE',
            121 => 'SALA DE COSTURA',
            122 => 'SALA DE FORCA',
            123 => 'SALA DE MANUTENCAO',
            124 => 'SALA DE MUSICA',
            125 => 'SALA DE REUNIÃO',
            126 => 'SALA EDICULA',
            127 => 'SALAO DE CULTO',
            128 => 'SANITARIO FEMININO',
            129 => 'SANITARIO MASCULINO',
            130 => 'SANITARIO DEFICIENTE',
            131 => 'TROCADOR FEMININO',
            132 => 'TROCADOR MASCULINO',
            134 => 'REFEITORIO',
            135 => 'TEMPLO',
            201 => 'ADMNISTRACAO',
            202 => 'COMPRAS',
            203 => 'CONTABILIDADE',
            204 => 'INFORMATICA',
            205 => 'JURIDICO',
            206 => 'PATRIMONIO',
            207 => 'RECURSOS HUMANOS',
            209 => 'SECRETARIA',
            210 => 'TESOURARIA',
            401 => 'FUNDO MUSICAL',
            475 => 'CONSELHO FISCAL',
            480 => 'ESTACIONAMENTO',
            501 => 'OP-ADMINISTRACAO DA PIEDADE',
            502 => 'OP-ALMOXARIFADO',
            503 => 'OP-ABRIGO DA PIEDADE',
            504 => 'OP-DORMITORIO DA PIEDADE',
            505 => 'OP-REFEITORIO DA PIEDADE',
            506 => 'OP-SALA COSTURA DA PIEDADE',
            511 => 'AMBULATÓRIO',
            512 => 'ENGENHARIA',
            513 => 'PORTARIA',
            514 => 'ESPAÇO INFANTIL',
            515 => 'SALA DE REUNIAO',
        ];

        foreach ($dependencias as $id => $nome) {
            DB::table('dependencias')->updateOrInsert(
                ['id' => $id],
                ['nome' => $nome, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependencias');
        Schema::dropIfExists('setores');
    }
};
