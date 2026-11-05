<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPresensi extends Model
{
    protected $table = 'sesi_presensi';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mapel_id',
        'jam_pelajaran_id',
        'jam_pelajaran',
        'tanggal',
        'barcode_token',
        'is_active',
        'tipe',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    /** Guru yang membuat sesi ini */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /** Kelas yang sedang diabsen */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /** Semua rekap presensi di sesi ini */
    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'sesi_presensi_id');
    }

    /** Mata Pelajaran sesi ini (opsional) */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    /** Jam Pelajaran sesi ini (relasi ke DB) */
    public function jamPelajaranRelation()
    {
        return $this->belongsTo(JamPelajaran::class, 'jam_pelajaran_id');
    }

    /** Acronym canonical mapping for Title Case formatter */
    const ACRONYM_MAP = [
        'KA' => 'KA',
        'PPLG' => 'PPLG',
        'TJKT' => 'TJKT',
        'ULP' => 'ULP',
        'ULW' => 'ULW',
        'FO' => 'FO',
        'MICE' => 'MICE',
        'PJOK' => 'PJOK',
        'IPA' => 'IPA',
        'IPS' => 'IPS',
        'KWU' => 'KWU',
        'IOT' => 'IoT',
        'UI/UX' => 'UI/UX',
        'OOP' => 'OOP',
        'K3LH' => 'K3LH',
        'GIM' => 'GIM',
        'HKL' => 'HKL',
        'KIN' => 'KIN',
        'TIK' => 'TIK',
    ];

    /**
     * Format nama mata pelajaran menjadi Title Case dengan preservasi akronim kejuruan.
     */
    public static function formatMapelTitle(?string $name): string
    {
        if (empty($name)) {
            return 'Presensi Kelas';
        }
        $name = trim($name);
        if (preg_match('/^koding\s*(dan|&)\s*ka$/i', $name)) {
            return str_contains($name, '&') ? 'Koding & KA' : 'Koding dan KA';
        }
        $words = preg_split('/(\s+|[-–—&])/', $name, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        foreach ($words as $w) {
            $trimmed = trim($w);
            if (empty($trimmed)) {
                $result .= $w;
                continue;
            }
            $upper = strtoupper($trimmed);
            if (isset(self::ACRONYM_MAP[$upper])) {
                $result .= self::ACRONYM_MAP[$upper];
            } elseif (in_array(strtolower($trimmed), ['dan', 'atau', 'di', 'ke', 'dari', 'yang', 'untuk', '&'])) {
                $result .= strtolower($trimmed);
            } else {
                $result .= mb_convert_case(mb_strtolower($trimmed), MB_CASE_TITLE, 'UTF-8');
            }
        }
        return $result;
    }

    /**
     * Format string jam pelajaran tanpa menduplikasi nama mata pelajaran.
     */
    public static function formatJamSession(?string $jam, ?string $mapel = null): string
    {
        if (empty($jam)) {
            return '';
        }
        $jam = trim($jam);
        if (preg_match('/^(.*?)\s*\((.*?)\)$/u', $jam, $matches)) {
            $prefix = trim($matches[1]);
            $inside = trim($matches[2]);
            $isMapelPrefix = false;
            if (!empty($mapel) && strcasecmp($prefix, trim($mapel)) === 0) {
                $isMapelPrefix = true;
            } elseif (!empty($prefix) && !preg_match('/^(jam|les|ke-|\d)/i', $prefix)) {
                $isMapelPrefix = true;
            }

            if ($isMapelPrefix) {
                if (preg_match('/^(.*?)\s*[•·-]\s*(\d{1,2}[\.:]\d{2}(?::\d{2})?\s*[-–—]\s*\d{1,2}[\.:]\d{2}(?::\d{2})?)$/u', $inside, $sub)) {
                    $sesiLabel = trim($sub[1]);
                    $timeRange = preg_replace('/(\d{1,2})[\.:](\d{2}):\d{2}/', '$1.$2', trim($sub[2]));
                    $timeRange = preg_replace('/[\s–—-]+/u', ' - ', $timeRange);
                    $timeRange = str_replace(':', '.', $timeRange);
                    return "{$sesiLabel} ({$timeRange})";
                }
                return $inside;
            } else {
                $timeRange = preg_replace('/(\d{1,2})[\.:](\d{2}):\d{2}/', '$1.$2', $inside);
                $timeRange = preg_replace('/[\s–—-]+/u', ' - ', $timeRange);
                $timeRange = str_replace(':', '.', $timeRange);
                return "{$prefix} ({$timeRange})";
            }
        }

        if (preg_match('/^(.*?)\s*[•·]\s*(.*)$/u', $jam, $matches)) {
            $sesiLabel = trim($matches[1]);
            $timeRange = trim($matches[2]);
            if (!empty($mapel) && stripos($sesiLabel, trim($mapel)) === 0) {
                $sesiLabel = trim(substr($sesiLabel, strlen(trim($mapel))));
            }
            $timeRange = preg_replace('/(\d{1,2})[\.:](\d{2}):\d{2}/', '$1.$2', $timeRange);
            $timeRange = preg_replace('/[\s–—-]+/u', ' - ', $timeRange);
            $timeRange = str_replace(':', '.', $timeRange);
            return (!empty($sesiLabel) ? "{$sesiLabel} " : '') . "({$timeRange})";
        }

        return $jam;
    }

    /** Accessor: Nama mapel yang rapi dalam Title Case */
    public function getFormattedMapelAttribute(): string
    {
        if ($this->mataPelajaran && !empty($this->mataPelajaran->nama_mapel)) {
            return static::formatMapelTitle($this->mataPelajaran->nama_mapel);
        }
        if (!empty($this->jam_pelajaran) && preg_match('/^(.*?)\s*\(/', $this->jam_pelajaran, $m)) {
            $extracted = trim($m[1]);
            if (!preg_match('/^(jam|les|ke-|\d)/i', $extracted)) {
                return static::formatMapelTitle($extracted);
            }
        }
        return 'Presensi Kelas';
    }

    /** Accessor: Sesi jam yang bersih tanpa nama mapel */
    public function getFormattedJamAttribute(): string
    {
        return static::formatJamSession($this->jam_pelajaran, $this->mataPelajaran?->nama_mapel);
    }
}
