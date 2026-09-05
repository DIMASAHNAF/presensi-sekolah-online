<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_geofencing_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'is_geofencing_active' => 'boolean',
    ];

    /**
     * Dapatkan konfigurasi sekolah (singleton record).
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'school_name' => 'SMK Negeri 1',
            'latitude' => -6.20876340,
            'longitude' => 106.84559900,
            'radius_meters' => 100,
            'is_geofencing_active' => false,
        ]);
    }
}
