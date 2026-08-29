<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SesiAbsensi;
use App\Models\LogAbsensi;
use Illuminate\Http\Request;

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

    public function scanBarcode(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $user = auth()->user();

        // Cari sesi aktif berdasarkan token
        $sesi = SesiAbsensi::where('barcode_token', $request->token)
            ->where('is_active', true)
            ->first();

        if (!$sesi) {
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

        // Cari record absensi siswa
        $absensi = Absensi::where('sesi_absensi_id', $sesi->id)
            ->where('siswa_id', $user->id)
            ->first();

        if (!$absensi) {
            return response()->json(['success' => false, 'message' => 'Data absensi Anda tidak ditemukan di sesi ini.']);
        }

        if ($absensi->status === 'hadir') {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan scan absen (Hadir).']);
        }

        $statusSebelumnya = $absensi->status;

        // Update jadi hadir
        $absensi->update([
            'status' => 'hadir',
            'keterangan' => 'Scan Mandiri (Sistem)',
        ]);

        // Catat log
        LogAbsensi::create([
            'absensi_id' => $absensi->id,
            'guru_id' => $sesi->guru_id, // Atas nama pembuat sesi atau sistem
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => 'hadir',
            'keterangan' => 'Siswa melakukan scan barcode mandiri',
        ]);

        return response()->json(['success' => true, 'message' => 'Berhasil! Anda tercatat HADIR hari ini.']);
    }
}
