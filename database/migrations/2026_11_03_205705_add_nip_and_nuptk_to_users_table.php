<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->after('nik')->index();
            $table->string('nuptk', 30)->nullable()->after('nip')->index();
        });

        // Auto-populate NIP for teachers whose username or nik is 18 digits (NIP ASN)
        DB::table('users')
            ->whereIn('role', ['guru', 'admin'])
            ->get()
            ->each(function ($user) {
                $nip = null;
                if (preg_match('/^\d{18}$/', (string) ($user->username ?? ''))) {
                    $nip = $user->username;
                } elseif (preg_match('/^\d{18}$/', (string) ($user->nik ?? ''))) {
                    $nip = $user->nik;
                }

                if ($nip) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['nip' => $nip]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['nip']);
            $table->dropIndex(['nuptk']);
            $table->dropColumn(['nip', 'nuptk']);
        });
    }
};
