<?php

namespace App\Enums\Landlord;

enum AnnouncementType: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case MAINTENANCE = 'maintenance';
    case UPDATE = 'update';

    public function label(): string
    {
        return match ($this) {
            self::INFO => 'Info',
            self::WARNING => 'Warning',
            self::MAINTENANCE => 'Maintenance',
            self::UPDATE => 'Update',
        };
    }
}
