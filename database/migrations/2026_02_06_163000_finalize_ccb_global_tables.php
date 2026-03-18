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
        // 1. Finalize Dependencias (Tabela de Localização)
        $dependencias = [
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
            125 => 'SALA DE REUNIAO',
            126 => 'SALA EDICULA',
            127 => 'SALAO DE CULTO',
            128 => 'SANITARIO FEMININO',
            129 => 'SANITARIO MASCULINO',
            130 => 'SANITARIO DEFICIENTE',
            131 => 'TROCADOR FEMININO',
            132 => 'TROCADOR MASCULINO',
            134 => 'REFEITORIO',
            201 => 'ADMNISTRACAO',
            209 => 'SECRETARIA',
            210 => 'TESOURARIA',
            301 => 'DISTRIBUIDORA',
            401 => 'FUNDO MUSICAL',
            501 => 'OP-ADMINISTRACAO DA PIEDADE',
            502 => 'OP-ALMOXARIFADO DA PIEDADE',
            503 => 'OP-ABRIGO DA PIEDADE',
            504 => 'OP-DORMITORIO DA PIEDADE',
            505 => 'OP-REFEITORIO DA PIEDADE',
            506 => 'OP-SALA COSTURA DA PIEDADE',
        ];

        foreach ($dependencias as $id => $nome) {
            DB::table('dependencias')->updateOrInsert(
                ['id' => $id],
                ['nome' => $nome, 'updated_at' => now()]
            );
        }

        // 2. Finalize Tipos de Bens (Refined list from images)
        $tiposExtra = [
            80 => ['AR CONDICIONADO / INSTALACOES', 1106],
            90 => ['BENFEITORIAS IMOVEIS DE TERCEIROS', 1109],
        ];

        // Ensure 1109 exists
        DB::table('contas_contabeis')->updateOrInsert(['id' => 1109], ['nome' => 'Benfeitorias Imóveis de Terceiros', 'updated_at' => now()]);

        foreach ($tiposExtra as $id => $data) {
            DB::table('tipos_bens')->updateOrInsert(
                ['id' => $id],
                ['nome' => $data[0], 'conta_contabil_id' => $data[1], 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed for seeding refinements
    }
};
