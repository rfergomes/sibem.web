<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Local;

class LocaisImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('Administrações_Novo.csv');

        if (!File::exists($csvFile)) {
            $this->command->error("CSV file not found: $csvFile");
            return;
        }

        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('locais')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file, 0, ';');

        // Remove BOM from the first element if present
        if (isset($header[0])) {
            $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
        }

        $this->command->info("Header keys: " . implode(', ', $header));
        $this->command->info("Importing Locais from $csvFile...");

        $count = 0;
        $batch = [];

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            // Skip empty rows
            if (empty($row) || (count($row) === 1 && is_null($row[0]))) {
                continue;
            }

            // Ensure row has same number of elements as header
            if (count($row) !== count($header)) {
                $this->command->warn("Row skipped due to mismatched column count. Row ID: " . ($row[0] ?? 'unknown'));
                continue;
            }

            $data = array_combine($header, $row);

            // Handle active field
            $active = 1;
            if (isset($data['active'])) {
                $statusVal = strtolower($data['active']);
                if ($statusVal === '0' || $statusVal === 'false') {
                    $active = 0;
                }
            }

            // Defaults for DB connection if empty
            $dbHost = !empty($data['db_host']) ? $data['db_host'] : '127.0.0.1';
            $dbUser = !empty($data['db_user']) ? $data['db_user'] : 'root';
            // Validation for foreign key regional_id could be added here, 
            // but we assume consistency or rely on FK constraints (which we re-enabled, 
            // so insertions might fail if integrity is broken, but we are using insert ignore or just valid data).
            // Actually 'insert' will fail if FK validation fails.

            $batch[] = [
                'id' => $data['id'],
                'regional_id' => $data['regional_id'],
                'nome' => $data['nome'],
                'razao_social' => $data['razao_social'] ?? null,
                'cnpj' => $data['cnpj'] ?? null,
                'cidade' => $data['cidade'] ?? null,
                'uf' => $data['uf'] ?? null,
                'status' => $data['id_status'] ?? 0,
                'db_host' => $dbHost,
                'db_name' => $data['db_name'] ?? ('sibem_adm_' . $data['id']), // Fallback if empty?
                'db_user' => $dbUser,
                'db_password' => $data['db_password'] ?? null,
                'active' => $active,
                'created_at' => ($data['created_at'] === 'NULL' || empty($data['created_at'])) ? now() : $data['created_at'],
                'updated_at' => ($data['updated_at'] === 'NULL' || empty($data['updated_at'])) ? now() : $data['updated_at'],
            ];

            $count++;

            if (count($batch) >= 100) {
                DB::table('locais')->insert($batch);
                $batch = [];
                $this->command->info("Imported $count rows...");
            }
        }

        if (!empty($batch)) {
            DB::table('locais')->insert($batch);
        }

        fclose($file);

        $this->command->info("Imported $count Locais successfully.");
    }
}
