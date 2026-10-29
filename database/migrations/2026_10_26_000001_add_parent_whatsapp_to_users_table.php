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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp_ortu', 30)->nullable()->after('email');
            $table->string('nama_ortu', 100)->nullable()->after('no_hp_ortu');
        });
    }

    /*
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_hp_ortu', 'nama_ortu']);
        });
    }
};
