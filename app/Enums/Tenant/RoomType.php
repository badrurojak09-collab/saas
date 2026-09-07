<?php

namespace App\Enums\Tenant;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RoomType: string implements HasColor, HasLabel
{
    case Classroom = 'classroom';
    case Laboratory = 'laboratory';
    case Auditorium = 'auditorium';
    case Workshop = 'workshop';
    case Library = 'library';

    public function getLabel(): string
    {
        return match ($this) {
            self::Classroom => 'Ruang Kelas',
            self::Laboratory => 'Laboratorium',
            self::Auditorium => 'Aula / Auditorium',
            self::Workshop => 'Bengkel / Workshop',
            self::Library => 'Perpustakaan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Classroom => 'info',
            self::Laboratory => 'primary',
            self::Auditorium => 'warning',
            self::Workshop => 'success',
            self::Library => 'purple',
        };
    }
}
