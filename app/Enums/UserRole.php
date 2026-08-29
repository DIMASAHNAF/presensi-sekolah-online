<?php

namespace App\Enums;

enum UserRole: string
{
    case SISWA = 'siswa';
    case GURU  = 'guru';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::SISWA => 'Siswa',
            self::GURU  => 'Guru',
            self::ADMIN => 'Admin',
        };
    }
}