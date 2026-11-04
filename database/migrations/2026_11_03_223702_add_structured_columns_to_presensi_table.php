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
        Schema::table('presensi', function (Blueprint $table) {
            $table->enum('check_in_status', ['ONTIME', 'LATE'])->default('ONTIME')->after('status');
            $table->unsignedInteger('late_minutes')->default(0)->after('check_in_status');
            $table->decimal('check_in_lat', 10, 8)->nullable()->after('late_minutes');
            $table->decimal('check_in_lng', 11, 8)->nullable()->after('check_in_lat');
            $table->decimal('check_in_distance', 8, 2)->nullable()->after('check_in_lng');
            $table->string('check_in_photo', 255)->nullable()->after('check_in_distance');

            $table->timestamp('check_out_time')->nullable()->after('waktu_scan');
            $table->decimal('check_out_lat', 10, 8)->nullable()->after('check_out_time');
            $table->decimal('check_out_lng', 11, 8)->nullable()->after('check_out_lat');
            $table->string('check_out_photo', 255)->nullable()->after('check_out_lng');

            $table->unsignedBigInteger('override_by')->nullable()->after('keterangan');
            $table->text('override_note')->nullable()->after('override_by');

            $table->foreign('override_by')->references('id')->on('users')->onDelete('set null');

            $table->index('check_in_status', 'idx_presensi_checkin_status');
            $table->index('late_minutes', 'idx_presensi_late_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropForeign(['override_by']);
            $table->dropIndex('idx_presensi_checkin_status');
            $table->dropIndex('idx_presensi_late_minutes');

            $table->dropColumn([
                'check_in_status',
                'late_minutes',
                'check_in_lat',
                'check_in_lng',
                'check_in_distance',
                'check_in_photo',
                'check_out_time',
                'check_out_lat',
                'check_out_lng',
                'check_out_photo',
                'override_by',
                'override_note',
            ]);
        });
    }
};
