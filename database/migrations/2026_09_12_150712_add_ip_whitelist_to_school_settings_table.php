<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->boolean('is_ip_whitelist_active')->default(false)->after('is_geofencing_active');
            $table->text('allowed_ips')->nullable()->after('is_ip_whitelist_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['is_ip_whitelist_active', 'allowed_ips']);
        });
    }
};
