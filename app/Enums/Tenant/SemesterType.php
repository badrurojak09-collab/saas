<?php

namespace App\Enums\Tenant;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SemesterType: string implements HasColor, HasLabel
{
    case Odd = 'odd';
    case Even = 'even';
    case Short = 'short';

    public function getLabel(): string
    {
        return match ($this) {
            self::Odd => 'Semester Ganjil',
            self::Even => 'Semester Genap',
            self::Short => 'Semester Antara (Pendek)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Odd => 'primary',
            self::Even => 'success',
            self::Short => 'warning',
        };
    }
}
