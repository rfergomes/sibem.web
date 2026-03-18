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
        Schema::table('inventarios', function (Blueprint $table) {
            $table->string('responsavel')->nullable()->after('user_id_abertura');
            $table->string('inventariante')->nullable()->after('responsavel');
            $table->boolean('is_sincronizado')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropColumn(['responsavel', 'inventariante', 'is_sincronizado']);
        });
    }
};
