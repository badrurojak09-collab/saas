<?php

namespace App\Enums\Landlord;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AddonStatus: string implements HasColor, HasLabel
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    /**
     * Mengembalikan teks label yang ramah pengguna di UI Filament.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    /**
     * Mengembalikan warna Badge pada Filament Admin Panel.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',  // Warna Hijau
            self::INACTIVE => 'gray',  // Warna Abu-abu
        };
    }
}
