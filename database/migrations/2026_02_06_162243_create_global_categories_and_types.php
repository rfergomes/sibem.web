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
        // 1. Contas Contábeis (Accounting Accounts)
        Schema::create('contas_contabeis', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // 1101, 1102, etc.
            $table->string('nome');
            $table->timestamps();
        });

        // 2. Tipos de Bens (Asset Types)
        Schema::create('tipos_bens', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // 1, 19, 64, etc.
            $table->string('nome');
            $table->unsignedBigInteger('conta_contabil_id');
            $table->timestamps();

            $table->foreign('conta_contabil_id')->references('id')->on('contas_contabeis');
        });

        // 3. Seed Accounts
        $accounts = [
            1100 => 'Construção/Reforma',
            1101 => 'Móveis e utensílios',
            1102 => 'Órgãos e Instrumentos',
            1103 => 'Máquinas e equipamentos',
            1105 => 'Equipamentos e sistemas',
            1106 => 'Instalações',
            1108 => 'Terrenos',
            11010 => 'Software',
        ];

        foreach ($accounts as $id => $nome) {
            DB::table('contas_contabeis')->updateOrInsert(['id' => $id], ['nome' => $nome, 'created_at' => now(), 'updated_at' => now()]);
        }

        // 4. Seed Asset Types
        $tipos = [
            1 => ['BANCO DE MADEIRA/GENUFLEXORIO', 1101],
            2 => ['TRIBUNA / CRIADO MUDO', 1101],
            3 => ['POLTRONA / SOFA', 1101],
            4 => ['CADEIRA', 1101],
            5 => ['GRADE MADEIRA P/ ORGAO', 1101],
            6 => ['REFRIGERADOR/FREEZER/FRIGOBAR', 1101],
            7 => ['MESA', 1101],
            8 => ['ARMARIO', 1101],
            9 => ['EQUIPAMENTOS DE LIMPEZA', 1103],
            10 => ['ARQUIVO / GAVETEIRO', 1101],
            11 => ['PRATELEIRA / ESTANTE', 1101],
            12 => ['BALCAO/BANCADA', 1101],
            13 => ['BEBEDOURO DAGUA / PURIFICADOR DE AGUA', 1101],
            14 => ['VENTILADOR', 1101],
            15 => ['RELOGIO DE PAREDE', 1101],
            16 => ['PAINEL DE CONTROLE DE SOM', 1101],
            17 => ['CAIXA DE SOM', 1101],
            18 => ['MICROFONE', 1101],
            19 => ['COMPUTADOR (CPU+MOUSE+TECLADO) / NOTEBOOK', 1105],
            20 => ['IMPRESSORA', 1105],
            21 => ['ORGAO E INSTRUMENTOS', 1102],
            22 => ['CALCULADORA', 1101],
            23 => ['EQUIPAMENTO DE ESCRITÓRIO', 1101],
            24 => ['MAQUINAS E EQUIPAMENTOS DE COSTURA', 1103],
            25 => ['EQUIPAMENTOS DE JARDINAGEM', 1103],
            26 => ['FORNO / FOGAO / MICROONDAS', 1101],
            50 => ['TERRENO', 1108],
            51 => ['EQUIPAMENTO MEDICO HOSPITALAR', 1101],
            52 => ['APARELHO TELEFONICO / APARELHO DE FAX', 1101],
            53 => ['COPIADORA (XEROX) / SCANNER', 1105],
            54 => ['COFRE', 1101],
            55 => ['ESCADA', 1101],
            56 => ['EXTINTOR', 1101],
            57 => ['LAVADORAS / TANQUE ELETRICO', 1101],
            58 => ['ESTANTES MUSICAIS E DE PARTITURAS / QUADROMUSICAL', 1101],
            59 => ['INVERSOR (NO-BREAK) / ESTABILIZADOR / CARREGADOR', 1101],
            60 => ['CONSTRUCAO', 1100],
            61 => ['CAIXA DE COLETA', 1101],
            62 => ['BANQUETA', 1101],
            63 => ['MONITOR /DATA SHOW', 1105],
            64 => ['ANDAIME - LATERAL/TRAVA/RODA', 1103],
            65 => ['FERRAMENTAS E MAQUINAS', 1103],
            66 => ['CAMAS / BELICHES', 1101],
            67 => ['TROCADOR PARA BEBE', 1101],
            68 => ['EQUIPAMENTOS DE CLIMATIZAÇÃO', 1101],
            69 => ['SOFTWARE', 11010],
            70 => ['REFORMA', 1100],
            80 => ['INSTALACOES', 1106],
            99 => ['DIVERSOS', 1101], // Added with fallback account
        ];

        foreach ($tipos as $id => $data) {
            DB::table('tipos_bens')->updateOrInsert(
                ['id' => $id],
                ['nome' => $data[0], 'conta_contabil_id' => $data[1], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_bens');
        Schema::dropIfExists('contas_contabeis');
    }
};
