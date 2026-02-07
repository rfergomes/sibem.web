<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BensImport;
use Illuminate\Support\Facades\Log;

class TestImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:test {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test import of a specific Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        $path = base_path($file);

        if (!file_exists($path)) {
            $this->error("File not found: $path");
            return 1;
        }

        $this->info("Starting import of $file...");

        try {
            // We need to set the connection to tenant manually if not handled by middleware in CLI is a concern,
            // but the models use 'tenant' connection which is defined in database.php.
            // Assuming default connection might be needed?
            // The Import uses models that use 'tenant' connection.
            // Let's ensure logging goes to stderr too

            Excel::import(new BensImport, $path);

            $this->info("Import completed successfully.");
        } catch (\Throwable $e) {
            $this->error("Import Failed!");

            $report = "Error Message: " . $e->getMessage() . "\n\n";
            $report .= "Stack Trace:\n" . $e->getTraceAsString();

            file_put_contents(base_path('error_report.txt'), $report);

            $this->error($e->getMessage());
        }
    }
}
