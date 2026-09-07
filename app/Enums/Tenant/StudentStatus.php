<?php

namespace App\Enums\Tenant;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StudentStatus: string implements HasColor, HasLabel
{
    case Prospective = 'prospective';
    case Active = 'active';
    case Leave = 'leave';
    case Inactive = 'inactive';
    case Graduated = 'graduated';
    case Dropout = 'dropout';
    case Withdrawn = 'withdrawn';

    public function getLabel(): string
    {
        return match ($this) {
            self::Prospective => 'Calon Mahasiswa',
            self::Active => 'Aktif',
            self::Leave => 'Cuti',
            self::Inactive => 'Nonaktif',
            self::Graduated => 'Lulus',
            self::Dropout => 'Drop Out',
            self::Withdrawn => 'Mengundurkan Diri',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Prospective => 'info',
            self::Active => 'success',
            self::Leave => 'warning',
            self::Inactive => 'gray',
            self::Graduated => 'primary',
            self::Dropout => 'danger',
            self::Withdrawn => 'danger',
        };
    }
}
