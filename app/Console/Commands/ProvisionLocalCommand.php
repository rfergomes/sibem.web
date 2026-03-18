<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Local;
use App\Services\TenantProvisioningService;

class ProvisionLocalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sibem:provision-local {id : The ID of the Local to provision}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes a new administration database and default data';

    /**
     * Execute the console command.
     */
    public function handle(TenantProvisioningService $provisioner)
    {
        $id = $this->argument('id');
        $local = Local::find($id);

        if (!$local) {
            $this->error("Local with ID {$id} not found.");
            return 1;
        }

        $this->info("Provisioning Local: {$local->nome}...");

        try {
            $provisioner->provision($local);
            $this->info("Provisioning completed successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Provisioning failed: " . $e->getMessage());
            return 1;
        }
    }
}
