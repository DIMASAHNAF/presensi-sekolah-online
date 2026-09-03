<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\LogPresensi;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user()->load('kelas');

        $riwayat = Presensi::where('siswa_id', $user->id)
            ->with(['sesiPresensi.kelas', 'sesiPresensi.mataPelajaran'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'hadir' => Presensi::where('siswa_id', $user->id)->where('status', 'hadir')->count(),
            'izin' => Presensi::where('siswa_id', $user->id)->where('status', 'izin')->count(),
            'sakit' => Presensi::where('siswa_id', $user->id)->where('status', 'sakit')->count(),
            'alpa' => Presensi::where('siswa_id', $user->id)->where('status', 'alpa')->count(),
        ];

        return view('siswa.dashboard', compact('user', 'riwayat', 'stats'));
    }

    public function scanBarcode(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = auth()->user();

        // Cari sesi aktif berdasarkan token
        $sesi = SesiPresensi::where('barcode_token', $request->token)
            ->where('is_active', true)
            ->first();

        if (! $sesi) {
            return response()->json(['success' => false, 'message' => 'Barcode tidak valid atau sesi sudah ditutup.']);
        }

        // Cek kelas
        if ($sesi->kelas_id !== $user->kelas_id) {
            return response()->json(['success' => false, 'message' => 'Ini bukan barcode kelas Anda.']);
        }

        // Cek expired (30 menit)
        if (now()->diffInMinutes($sesi->created_at) >= 30) {
            return response()->json(['success' => false, 'message' => 'Batas waktu scan 30 menit sudah habis. Silakan hubungi guru.']);
        }

        // Cari record presensi siswa
        $presensi = Presensi::where('sesi_presensi_id', $sesi->id)
            ->where('siswa_id', $user->id)
            ->first();

        if (! $presensi) {
            // Jika siswa baru register setelah sesi dibuat, buatkan recordnya sekarang
            $presensi = Presensi::create([
                'sesi_presensi_id' => $sesi->id,
                'siswa_id' => $user->id,
                'status' => 'alpa',
            ]);
        }

        if ($presensi->status === 'hadir') {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan scan absen (Hadir).']);
        }

        $statusSebelumnya = $presensi->status;

        // Update jadi hadir
        $presensi->update([
            'status' => 'hadir',
            'waktu_scan' => now(),
            'keterangan' => 'Scan Mandiri (Sistem)',
        ]);

        // Catat log
        LogPresensi::create([
            'presensi_id' => $presensi->id,
            'guru_id' => $sesi->guru_id, // Atas nama pembuat sesi atau sistem
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => 'hadir',
            'keterangan' => 'Siswa melakukan scan barcode mandiri',
        ]);

        return response()->json(['success' => true, 'message' => 'Berhasil! Anda tercatat HADIR hari ini.']);
    }
}
