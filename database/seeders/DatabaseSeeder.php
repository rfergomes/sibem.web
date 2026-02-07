<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Perfis (Global)
        $perfis = [
            ['nome' => 'Admin Sistema', 'slug' => 'admin_sistema'],
            ['nome' => 'Admin Regional', 'slug' => 'admin_regional'],
            ['nome' => 'Admin Local', 'slug' => 'admin_local'],
            ['nome' => 'Operador', 'slug' => 'operador'],
            ['nome' => 'Auditor', 'slug' => 'auditor'],
        ];

        foreach ($perfis as $p) {
            DB::table('perfis')->updateOrInsert(
                ['slug' => $p['slug']],
                ['nome' => $p['nome'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $pAdminSistema = DB::table('perfis')->where('slug', 'admin_sistema')->first()->id;
        $pAdminRegional = DB::table('perfis')->where('slug', 'admin_regional')->first()->id;
        $pAdminLocal = DB::table('perfis')->where('slug', 'admin_local')->first()->id;

        // 1. Create Regionais
        $idRegional = DB::table('regionais')->insertGetId([
            'nome' => 'Regional Campinas',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Create Locais (Tenants)
        $idLocal = DB::table('locais')->insertGetId([
            'regional_id' => $idRegional,
            'nome' => 'Adm. Campinas',
            'db_host' => '50.116.86.24', // Database Host
            'db_name' => 'sibemo33_cps', // Correct Tenant DB
            'db_user' => 'sibemo33_admin', // Assuming same user for now
            'db_password' => 'Sibem@2026', // Password from .env
            'active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 3. Create Users
        $idUserLocal = DB::table('users')->insertGetId([
            'nome' => 'Admin Local CPS',
            'email' => 'local_cps@sibem.ccb.org.br',
            'password' => Hash::make('password'),
            'perfil_id' => $pAdminLocal,
            'regional_id' => $idRegional,
            'local_id' => $idLocal, // Keep for legacy
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Attach to Pivot
        DB::table('local_user')->insert([
            'user_id' => $idUserLocal,
            'local_id' => $idLocal,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Adm Geral and Regional
        DB::table('users')->insert([
            [
                'nome' => 'Administrador Geral',
                'email' => 'admin@sibem.ccb.org.br',
                'password' => Hash::make('password'),
                'perfil_id' => $pAdminSistema,
                'regional_id' => null,
                'local_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nome' => 'Admin Regional Campinas',
                'email' => 'reg_campinas@sibem.ccb.org.br',
                'password' => Hash::make('password'),
                'perfil_id' => $pAdminRegional,
                'regional_id' => $idRegional,
                'local_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
