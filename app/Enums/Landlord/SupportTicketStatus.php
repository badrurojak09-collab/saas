<?php

namespace App\Enums\Landlord;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SupportTicketStatus: string implements HasColor, HasLabel
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_CLIENT = 'waiting_client';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::WAITING_CLIENT => 'Waiting Client',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::OPEN => 'danger',
            self::IN_PROGRESS => 'warning',
            self::WAITING_CLIENT => 'info',
            self::RESOLVED => 'success',
            self::CLOSED => 'gray',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }
}
