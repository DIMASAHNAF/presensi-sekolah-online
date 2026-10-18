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
            $table->longText('polygon_coordinates')->nullable()->after('radius_meters');
            $table->string('geofence_mode', 20)->default('radius')->after('polygon_coordinates'); // 'radius' atau 'polygon'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['polygon_coordinates', 'geofence_mode']);
        });
    }
};
