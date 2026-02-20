<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    public function run()
    {
        // Seed Status Bens (Tenant-specific)
        $status = [
            ['id' => 1, 'nome' => 'Ativo', 'contabiliza' => true],
            ['id' => 2, 'nome' => 'Em Manutenção', 'contabiliza' => true],
            ['id' => 3, 'nome' => 'Baixado', 'contabiliza' => false],
            ['id' => 4, 'nome' => 'Furtado/Roubado', 'contabiliza' => false],
        ];

        foreach ($status as $st) {
            DB::connection('tenant')->table('status_bens')->updateOrInsert(
                ['id' => $st['id']],
                array_merge($st, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
