<?php

namespace App\Enums\Landlord;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatus: string implements HasColor, HasLabel
{
    case TRIAL = 'trial';
    case TRIALING = 'trialing';
    case ACTIVE = 'active';
    case PAST_DUE = 'past_due';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::TRIAL, self::TRIALING => 'Trial',
            self::ACTIVE => 'Active',
            self::PAST_DUE => 'Past Due',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::TRIAL, self::TRIALING => 'info',
            self::ACTIVE => 'success',
            self::PAST_DUE => 'warning',
            self::EXPIRED => 'danger',
            self::CANCELLED => 'gray',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }
}
