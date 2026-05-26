<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = 'mysql_sys';

        // Drop table if exists to be safe and clean
        DB::connection($connection)->statement('DROP TABLE IF EXISTS tipos_imovel');

        Schema::connection($connection)->create('tipos_imovel', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->timestamps();
        });

        // Import data from tipos_imovel.csv in the root
        $csvFile = base_path('tipos_imovel.csv');
        if (file_exists($csvFile)) {
            $file = fopen($csvFile, 'r');
            // Skip header: "id";"nome";"created_at";"updated_at"
            fgetcsv($file, 0, ';');
            
            while (($row = fgetcsv($file, 0, ';')) !== FALSE) {
                if (count($row) >= 2) {
                    $nome = trim($row[1], " \t\n\r\0\x0B\"");
                    // Convert from ISO-8859-1 to UTF-8 to handle accents correctly
                    $nome = mb_convert_encoding($nome, 'UTF-8', 'ISO-8859-1');
                    
                    DB::connection($connection)->table('tipos_imovel')->insert([
                        'id' => intval($row[0]),
                        'nome' => $nome,
                        'created_at' => !empty($row[2]) ? trim($row[2], "\"") : now(),
                        'updated_at' => !empty($row[3]) ? trim($row[3], "\"") : now(),
                    ]);
                }
            }
            fclose($file);
        }

        // Add foreign key constraint to description table using raw SQL
        try {
            DB::connection($connection)->statement('ALTER TABLE igrejas_v2 ADD CONSTRAINT fk_igrejas_tipo_id FOREIGN KEY (tipo_id) REFERENCES tipos_imovel(id) ON DELETE RESTRICT');
        } catch (\Exception $e) {
            logger()->warning("Could not add foreign key constraint: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = 'mysql_sys';

        try {
            DB::connection($connection)->statement('ALTER TABLE igrejas_v2 DROP FOREIGN KEY fk_igrejas_tipo_id');
        } catch (\Exception $e) {
            // Ignore if constraint doesn't exist
        }

        DB::connection($connection)->statement('DROP TABLE IF EXISTS tipos_imovel');
    }
};
