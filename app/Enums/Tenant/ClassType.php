<?php

namespace App\Enums\Tenant;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ClassType: string implements HasColor, HasLabel
{
    case Regular = 'regular';
    case Evening = 'evening';
    case International = 'international';
    case Online = 'online';
    case Hybrid = 'hybrid';

    public function getLabel(): string
    {
        return match ($this) {
            self::Regular => 'Reguler',
            self::Evening => 'Kelas Malam / Karyawan',
            self::International => 'Internasional',
            self::Online => 'Online',
            self::Hybrid => 'Hybrid',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Regular => 'primary',
            self::Evening => 'info',
            self::International => 'warning',
            self::Online => 'success',
            self::Hybrid => 'purple',
        };
    }
}
