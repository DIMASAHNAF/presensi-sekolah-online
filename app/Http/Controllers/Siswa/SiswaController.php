<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user()->load('kelas');

        $riwayat = Absensi::where('siswa_id', $user->id)
            ->with('sesiAbsensi.kelas')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'hadir' => Absensi::where('siswa_id', $user->id)->where('status', 'hadir')->count(),
            'izin'  => Absensi::where('siswa_id', $user->id)->where('status', 'izin')->count(),
            'sakit' => Absensi::where('siswa_id', $user->id)->where('status', 'sakit')->count(),
            'alpa'  => Absensi::where('siswa_id', $user->id)->where('status', 'alpa')->count(),
        ];

        return view('siswa.dashboard', compact('user', 'riwayat', 'stats'));
    }
}
