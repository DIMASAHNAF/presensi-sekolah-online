<?php

namespace App\Services;

use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $token;
    protected string $apiUrl;
    protected string $jamMasuk;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->apiUrl = config('services.fonnte.url', 'https://api.fonnte.com/send');
        $this->jamMasuk = config('services.fonnte.jam_masuk', '07:30');
    }

    /**
     * Kirim pesan WhatsApp ke nomor tujuan via Fonnte Gateway.
     */
    public function send(string $target, string $message): array
    {
        if (empty($this->token)) {
            Log::warning('[WhatsAppService] Gagal kirim WA: FONNTE_TOKEN belum diset di .env');
            return ['status' => false, 'message' => 'Token WhatsApp belum diset.'];
        }

        // Normalisasi nomor telepon Indonesia (misal 0812... -> 0812... atau +62812... -> 0812...)
        $cleanTarget = preg_replace('/[^0-9]/', '', $target);

        try {
            $response = Http::timeout(3)
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->asForm()
                ->post($this->apiUrl, [
                    'target'  => $cleanTarget,
                    'message' => $message,
                ]);

            $result = $response->json() ?? [];
            Log::info("[WhatsAppService] Terkirim ke {$cleanTarget}: " . json_encode($result));
            return [
                'status' => $response->successful() && ($result['status'] ?? false),
                'data'   => $result,
            ];
        } catch (\Throwable $e) {
            Log::error("[WhatsAppService] Exception saat kirim ke {$cleanTarget}: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Kirim notifikasi kehadiran siswa ke nomor WhatsApp Orang Tua.
     */
    public function sendPresensiNotification(Presensi $presensi, bool $force = false): bool
    {
        $siswa = $presensi->siswa;
        if (! $siswa || empty($siswa->no_hp_ortu)) {
            return false;
        }

        $sesi = $presensi->sesiPresensi;
        $tanggal = $sesi?->tanggal ? Carbon::parse($sesi->tanggal)->format('Y-m-d') : today()->format('Y-m-d');

        // Mencegah pesan dobel/spam dalam 1 hari untuk siswa yang sama
        $cacheKey = "wa_presensi_sent_{$siswa->id}_{$tanggal}";
        if (! $force && Cache::has($cacheKey)) {
            return false;
        }

        // Tentukan waktu scan (jam & menit) dengan timezone Asia/Jakarta
        $waktuScan = $presensi->waktu_scan
            ? Carbon::parse($presensi->waktu_scan)->timezone('Asia/Jakarta')
            : now('Asia/Jakarta');
        $jamScanStr = $waktuScan->format('H:i');

        // Evaluasi keterlambatan berdasarkan jam masuk sekolah (07:30 WIB)
        $isLate = ($presensi->check_in_status === 'LATE') || ($presensi->late_minutes > 0);
        $lateMinutes = (int) ($presensi->late_minutes ?? 0);

        if (!$isLate) {
            $jamMasukTime = Carbon::parse($waktuScan->format('Y-m-d') . ' ' . $this->jamMasuk, 'Asia/Jakarta');
            if ($waktuScan->greaterThan($jamMasukTime)) {
                $diff = (int) round(abs($waktuScan->diffInMinutes($jamMasukTime, true)));
                if ($diff > 0) {
                    $isLate = true;
                    $lateMinutes = $diff;
                }
            }
        }

        // HANYA kirim notifikasi WhatsApp ke orang tua jika siswa terlambat
        // Siswa tepat waktu tidak membebani gateway WhatsApp & langsung selesai instan
        if (! $isLate) {
            return false;
        }

        if ($lateMinutes >= 60) {
            $jam = intdiv($lateMinutes, 60);
            $menit = $lateMinutes % 60;
            $keteranganTerlambat = $menit > 0 ? "{$jam} Jam {$menit} Menit" : "{$jam} Jam";
        } else {
            $keteranganTerlambat = "{$lateMinutes} Menit";
        }

        $namaSiswa = $siswa->name;
        $namaKelas = $siswa->kelas?->nama_kelas ?? ($sesi?->kelas?->nama_kelas ?? '-');
        $namaOrtu = $siswa->nama_ortu ?: 'Bapak/Ibu Wali Murid';

        $pesan = "Assalamu’alaikum Warahmatullahi Wabarakatuh / Selamat Pagi,\n"
            . "Yth. {$namaOrtu},\n\n"
            . "Diberitahukan bahwa ananda:\n"
            . "👤 *{$namaSiswa}*\n"
            . "🏫 Kelas: *{$namaKelas}*\n"
            . "🕒 Waktu Tiba: *{$jamScanStr} WIB*\n"
            . "⚠️ Status: *Hadir (Terlambat {$keteranganTerlambat})*\n"
            . "📍 Jam Masuk Sekolah: *07:30 WIB*\n\n"
            . "Ananda telah sampai di *SMKN 1 Beringin* dan tercatat hadir terlambat pada sistem presensi biometrik.\n\n"
            . "Mohon bantuan Bapak/Ibu untuk turut mengingatkan ananda agar dapat berangkat lebih awal demi kedisiplinan belajar di sekolah.\n\n"
            . "_Pesan otomatis resmi dari Sistem Presensi SMKN 1 Beringin_";

        $res = $this->send($siswa->no_hp_ortu, $pesan);

        if ($res['status']) {
            Cache::put($cacheKey, true, now()->endOfDay());
            return true;
        }

        return false;
    }
}
