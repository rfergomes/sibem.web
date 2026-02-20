<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOpenInventories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-open-inventories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting check for open inventories...');

        $locais = \App\Models\Local::where('active', true)->get();
        $notifService = app(\App\Services\NotificationService::class);
        $totalNotifications = 0;

        foreach ($locais as $local) {
            $this->comment("Checking Local: {$local->nome}");

            try {
                // Configure tenant database connection
                config([
                    'database.connections.tenant.host' => $local->db_host,
                    'database.connections.tenant.database' => $local->db_name,
                    'database.connections.tenant.username' => $local->db_user,
                    'database.connections.tenant.password' => $local->db_password,
                ]);

                // Purge connection to ensure new settings are used
                \Illuminate\Support\Facades\DB::purge('tenant');

                // Find open inventories older than 7 days
                $openInventories = \App\Models\Inventario::where('status', 'aberto')
                    ->where('created_at', '<', now()->subDays(7))
                    ->get();

                foreach ($openInventories as $inventory) {
                    // Set local_id on the model for the notification service
                    $inventory->local_id = $local->id;

                    $daysOpen = now()->diffInDays($inventory->created_at);

                    $notifService->createInventoryOpenNotification($inventory, $daysOpen);
                    $totalNotifications++;
                }

            } catch (\Exception $e) {
                $this->error("Error checking local {$local->nome}: " . $e->getMessage());
            }
        }

        $this->info("Done! Created {$totalNotifications} notifications.");
    }
}
