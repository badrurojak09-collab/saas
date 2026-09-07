<?php

namespace App\Enums\Landlord;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TenantDatabaseStatus: string implements HasColor, HasLabel
{
    case PROVISIONING = 'provisioning';
    case CREATING = 'creating';
    case MIGRATING = 'migrating';
    case READY = 'ready';
    case MAINTENANCE = 'maintenance';
    case FAILED = 'failed';
    case UNREACHABLE = 'unreachable';
    case DECOMMISSIONED = 'decommissioned';

    /**
     * Mengembalikan teks label yang ramah pengguna.
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROVISIONING => 'Provisioning',
            self::CREATING => 'Creating',
            self::MIGRATING => 'Migrating',
            self::READY => 'Ready',
            self::MAINTENANCE => 'Maintenance',
            self::FAILED => 'Failed',
            self::UNREACHABLE => 'Unreachable',
            self::DECOMMISSIONED => 'Decommissioned',
        };
    }

    /**
     * Mengembalikan warna Badge di Filament Panel.
     */
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PROVISIONING, self::CREATING => 'info',  // Warna Biru
            self::MIGRATING => 'warning',  // Warna Kuning
            self::READY => 'success',  // Warna Hijau
            self::MAINTENANCE, self::DECOMMISSIONED => 'gray',  // Warna Abu-abu
            self::FAILED, self::UNREACHABLE => 'danger',  // Warna Merah
        };
    }
}
