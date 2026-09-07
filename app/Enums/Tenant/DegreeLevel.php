<?php

namespace App\Enums\Tenant;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DegreeLevel: string implements HasColor, HasLabel
{
    case D1 = 'd1';
    case D2 = 'd2';
    case D3 = 'd3';
    case D4 = 'd4';
    case S1 = 's1';
    case S2 = 's2';
    case S3 = 's3';
    case Profesi = 'profesi';

    public function getLabel(): string
    {
        return match ($this) {
            self::D1 => 'Diploma 1 (D1)',
            self::D2 => 'Diploma 2 (D2)',
            self::D3 => 'Diploma 3 (D3)',
            self::D4 => 'Sarjana Terapan (D4)',
            self::S1 => 'Sarjana (S1)',
            self::S2 => 'Magister (S2)',
            self::S3 => 'Doktor (S3)',
            self::Profesi => 'Profesi',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::D1, self::D2, self::D3 => 'info',
            self::D4, self::S1 => 'primary',
            self::S2 => 'success',
            self::S3 => 'warning',
            self::Profesi => 'purple',
        };
    }
}
