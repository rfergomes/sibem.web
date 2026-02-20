<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class IgrejasImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '1024M');

        $csvFile = base_path('Igrejas_Novo.csv');

        if (!File::exists($csvFile)) {
            $this->command->error("CSV file not found: $csvFile");
            return;
        }

        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('igrejas_global')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file, 0, ';');

        // Remove BOM from the first element if present
        if (isset($header[0])) {
            $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
        }

        $this->command->info("Header keys: " . implode(', ', $header));
        $this->command->info("Importing Igrejas from $csvFile...");

        $count = 0;
        $batch = [];

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            // Skip empty rows
            if (empty($row) || (count($row) === 1 && is_null($row[0]))) {
                continue;
            }

            // Ensure row has same number of elements as header
            if (count($row) !== count($header)) {
                // Try to pad with nulls if it's just missing trailing columns? 
                // Or warn and skip. Let's warn and skip to be safe.
                $this->command->warn("Row skipped due to mismatched column count. ID: " . ($row[0] ?? 'unknown'));
                continue;
            }

            $data = array_combine($header, $row);

            // Handle potential empty strings for integer/nullable fields to avoid SQL errors
            $localId = !empty($data['local_id']) ? $data['local_id'] : null;
            $legacyId = !empty($data['legacy_id']) ? $data['legacy_id'] : null;
            $idStatus = !empty($data['id_status']) ? $data['id_status'] : null;
            $idTipo = !empty($data['id_tipo']) ? $data['id_tipo'] : null;

            $batch[] = [
                'id' => $data['id'],
                'legacy_id' => $legacyId,
                'local_id' => $localId,
                'setor' => $data['setor'] ?? null,
                'codigo_ccb' => $data['codigo_ccb'] ?? null,
                'nome' => $data['nome'],
                'cidade' => $data['cidade'] ?? null,
                'bairro' => $data['bairro'] ?? null,
                'razao_social' => $data['razao_social'] ?? null,
                'cnpj' => $data['cnpj'] ?? null,
                'logradouro' => $data['logradouro'] ?? null,
                'numero' => $data['numero'] ?? null,
                'uf' => $data['uf'] ?? null,
                'observacao' => $data['observacao'] ?? null,
                'id_status' => $idStatus,
                'id_tipo' => $idTipo,
                'created_at' => $this->transformDate($data['created_at']),
                'updated_at' => $this->transformDate($data['updated_at']),
            ];

            $count++;

            if (count($batch) >= 1000) {
                DB::table('igrejas_global')->upsert($batch, ['id'], [
                    'legacy_id',
                    'local_id',
                    'codigo_ccb',
                    'nome',
                    'cidade',
                    'bairro',
                    'uf',
                    'id_status',
                    'id_tipo',
                    'created_at',
                    'updated_at',
                    'logradouro',
                    'numero',
                    'observacao',
                    'cnpj',
                    'setor',
                    'razao_social'
                ]);
                $batch = [];
                $this->command->info("Imported $count rows...");
            }
        }

        if (!empty($batch)) {
            DB::table('igrejas_global')->upsert($batch, ['id'], [
                'legacy_id',
                'local_id',
                'codigo_ccb',
                'nome',
                'cidade',
                'bairro',
                'uf',
                'id_status',
                'id_tipo',
                'created_at',
                'updated_at',
                'logradouro',
                'numero',
                'observacao',
                'cnpj',
                'setor',
                'razao_social'
            ]);
        }

        fclose($file);

        $this->command->info("Imported $count Igrejas successfully.");
    }

    private function transformDate($value)
    {
        if (empty($value) || $value === 'NULL') {
            return now();
        }

        try {
            // Replace comma with dot for numeric check
            $numericValue = str_replace(',', '.', $value);

            if (is_numeric($numericValue)) {
                try {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($numericValue);
                } catch (\Exception $e) {
                    return now();
                }
            }

            // Try standard parsing
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return now();
        }
    }
}
