<?php

/**
 * Database Cleanup Script
 * Removes redundant tables from the Tenant database (sibemo33_cps)
 * 
 * Usage: php database/scripts/cleanup_tenant_db.php
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Database Cleanup Script ===\n";
echo "Target: sibemo33_cps (Tenant Database)\n\n";

// Tables to remove (redundant - managed in System DB)
$tablesToDrop = [
    'users',
    'locais',
    'regionais',
    'igrejas_global',
    'dependencias',
    'tipos_bens',
    'local_user',
    'perfis',
    'solicitacao_acessos',
    'password_reset_tokens',
    'failed_jobs',
    'personal_access_tokens',
    'setores',
];

// Step 1: List all current tables
echo "Step 1: Listing current tables in sibemo33_cps...\n";
$currentTables = DB::connection('tenant')->select('SHOW TABLES');
$currentTableNames = array_map(function ($table) {
    return array_values((array) $table)[0];
}, $currentTables);

echo "Found " . count($currentTableNames) . " tables:\n";
foreach ($currentTableNames as $table) {
    echo "  - $table\n";
}
echo "\n";

// Step 2: Identify tables to drop
echo "Step 2: Identifying redundant tables...\n";
$existingRedundant = array_intersect($tablesToDrop, $currentTableNames);

if (empty($existingRedundant)) {
    echo "✅ No redundant tables found. Database is already clean!\n";
    exit(0);
}

echo "Found " . count($existingRedundant) . " redundant tables to remove:\n";
foreach ($existingRedundant as $table) {
    echo "  ❌ $table\n";
}
echo "\n";

// Step 3: Drop tables
echo "Step 3: Dropping redundant tables...\n";
$droppedCount = 0;
$errors = [];

foreach ($existingRedundant as $table) {
    try {
        DB::connection('tenant')->statement("DROP TABLE IF EXISTS `$table`");
        echo "  ✅ Dropped: $table\n";
        $droppedCount++;
    } catch (\Exception $e) {
        $error = "  ❌ Failed to drop $table: " . $e->getMessage();
        echo "$error\n";
        $errors[] = $error;
    }
}

echo "\n";
echo "=== Cleanup Summary ===\n";
echo "Tables dropped: $droppedCount\n";
echo "Errors: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "\nError Details:\n";
    foreach ($errors as $error) {
        echo "$error\n";
    }
}

// Step 4: List remaining tables
echo "\nStep 4: Listing remaining tables...\n";
$remainingTables = DB::connection('tenant')->select('SHOW TABLES');
$remainingTableNames = array_map(function ($table) {
    return array_values((array) $table)[0];
}, $remainingTables);

echo "Remaining tables (" . count($remainingTableNames) . "):\n";
foreach ($remainingTableNames as $table) {
    echo "  ✅ $table\n";
}

echo "\n✅ Cleanup completed successfully!\n";
