<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    public function run()
    {
        // 1. Setores
        $setores = [
            ['nome' => 'Administração'],
            ['nome' => 'Nave / Templo'],
            ['nome' => 'Cozinha / Refeitório'],
            ['nome' => 'Externo'],
        ];

        foreach ($setores as $s) {
            DB::connection('tenant')->table('setores')->insert(array_merge($s, ['created_at' => now(), 'updated_at' => now()]));
        }

        $idAdm = DB::connection('tenant')->table('setores')->where('nome', 'Administração')->value('id');
        $idTemplo = DB::connection('tenant')->table('setores')->where('nome', 'Nave / Templo')->value('id');

        // 2. Dependências
        $deps = [
            ['setor_id' => $idAdm, 'nome' => 'Escritório'],
            ['setor_id' => $idAdm, 'nome' => 'Sala de Reunião'],
            ['setor_id' => $idTemplo, 'nome' => 'Galeria'],
            ['setor_id' => $idTemplo, 'nome' => 'Púlpito'],
        ];

        foreach ($deps as $d) {
            DB::connection('tenant')->table('dependencias')->insert(array_merge($d, ['created_at' => now(), 'updated_at' => now()]));
        }

        // 3. Status Bens
        $status = [
            ['nome' => 'Ativo', 'contabiliza' => true],
            ['nome' => 'Em Manutenção', 'contabiliza' => true],
            ['nome' => 'Baixado', 'contabiliza' => false],
            ['nome' => 'Furtado/Roubado', 'contabiliza' => false],
        ];

        foreach ($status as $st) {
            DB::connection('tenant')->table('status_bens')->insert(array_merge($st, ['created_at' => now(), 'updated_at' => now()]));
        }
    }
}
