<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoImovelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            'TERRENO',
        ];

        foreach ($tipos as $tipo) {
            \App\Models\TipoImovel::firstOrCreate(['nome' => $tipo]);
        }
    }
}
