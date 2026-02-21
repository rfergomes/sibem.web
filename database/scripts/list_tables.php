<?php

/**
 * List Tables Script
 * Lists all tables in both System and Tenant databases
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Database Tables Report ===\n\n";

// System Database
echo "📊 SYSTEM DATABASE (sibemo33_adm):\n";
$systemTables = DB::connection('mysql')->select('SHOW TABLES');
$systemTableNames = array_map(function ($table) {
    return array_values((array) $table)[0];
}, $systemTables);

sort($systemTableNames);
foreach ($systemTableNames as $table) {
    echo "  ✅ $table\n";
}
echo "Total: " . count($systemTableNames) . " tables\n\n";

// Tenant Database
echo "📊 TENANT DATABASE (sibemo33_cps):\n";
$tenantTables = DB::connection('tenant')->select('SHOW TABLES');
$tenantTableNames = array_map(function ($table) {
    return array_values((array) $table)[0];
}, $tenantTables);

sort($tenantTableNames);
foreach ($tenantTableNames as $table) {
    echo "  ✅ $table\n";
}
echo "Total: " . count($tenantTableNames) . " tables\n";
