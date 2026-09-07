<?php

namespace App\Enums\Landlord;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProvisioningJobStatus: string implements HasColor, HasLabel
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case PROVISIONING = 'provisioning';
    case PROCESSING = 'processing';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::RUNNING => 'Berjalan',
            self::COMPLETED => 'Selesai',
            self::FAILED => 'Gagal',
            self::PROVISIONING => 'Provisioning',
            self::PROCESSING => 'Memproses',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::RUNNING, self::PROVISIONING, self::PROCESSING => 'info',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'gray',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }
}
