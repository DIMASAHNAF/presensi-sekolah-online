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
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('SMK Negeri 1');
            $table->decimal('latitude', 10, 8)->default(-6.20876340);
            $table->decimal('longitude', 11, 8)->default(106.84559900);
            $table->unsignedInteger('radius_meters')->default(100);
            $table->boolean('is_geofencing_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
