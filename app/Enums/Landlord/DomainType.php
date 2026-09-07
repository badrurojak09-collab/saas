<?php

namespace App\Enums\Landlord;

enum DomainType: string
{
    case SUBDOMAIN = 'subdomain';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::SUBDOMAIN => 'Subdomain',
            self::CUSTOM => 'Custom Domain',
        };
    }
}
