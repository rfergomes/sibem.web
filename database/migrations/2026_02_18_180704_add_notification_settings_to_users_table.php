<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('notification_settings')->nullable()->after('active');
        });

        // Initialize default settings for existing users
        $defaultSettings = json_encode([
            'access_request' => true,
            'inventory_open' => true,
            'access_request_status' => true,
            'sound_enabled' => true,
            'browser_push' => false,
        ]);

        \DB::table('users')->update(['notification_settings' => $defaultSettings]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notification_settings');
        });
    }
};
