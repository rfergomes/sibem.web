<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Regional;

class RegionaisImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('Regionais_Novo.csv');

        if (!File::exists($csvFile)) {
            $this->command->error("CSV file not found: $csvFile");
            return;
        }

        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file, 0, ';'); // Assuming semicolon delimiter based on previous file views

        // Remove BOM from the first element if present
        if (isset($header[0])) {
            $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
        }

        $this->command->info("Header keys: " . implode(', ', $header));

        $this->command->info("Truncating table regionais...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('regionais')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info("Table truncated.");

        $this->command->info("Importing Regionais from $csvFile...");

        $count = 0;
        $batch = [];
        while (($row = fgetcsv($file, 0, ';')) !== false) {
            // Skip empty rows
            if (empty($row) || (count($row) === 1 && is_null($row[0]))) {
                continue;
            }

            // Ensure row has same number of elements as header
            if (count($row) !== count($header)) {
                $this->command->warn("Row skipped due to mismatched column count. Row: " . implode(';', $row));
                continue;
            }

            $data = array_combine($header, $row);

            // Handle active field
            $active = 1; // Default true (1)
            if (isset($data['active'])) {
                $status = strtolower($data['active']);
                if ($status === '0' || $status === 'false') {
                    $active = 0;
                }
            }

            $batch[] = [
                'id' => $data['id'],
                'nome' => $data['nome'],
                'uf' => $data['uf'],
                'active' => $active,
                'created_at' => ($data['created_at'] === 'NULL' || empty($data['created_at'])) ? now() : $data['created_at'],
                'updated_at' => ($data['updated_at'] === 'NULL' || empty($data['updated_at'])) ? now() : $data['updated_at'],
            ];

            $count++;

            if (count($batch) >= 100) {
                DB::table('regionais')->insert($batch);
                $batch = [];
                $this->command->info("Imported $count rows...");
            }
        }

        if (!empty($batch)) {
            DB::table('regionais')->insert($batch);
        }

        fclose($file);

        $this->command->info("Imported $count regionais successfully.");
    }
}
