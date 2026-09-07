<?php

namespace App\Enums\Landlord;

enum ProvisioningStep: string
{
    case CREATE_DATABASE = 'create_database';
    case MIGRATE_DATABASE = 'migrate_database';
    case SEED_DATABASE = 'seed_database';
    case CREATE_ADMIN = 'create_admin';
    case HEALTH_CHECK = 'health_check';
    case COMPLETE = 'complete';

    public function label(): string
    {
        return match ($this) {
            self::CREATE_DATABASE => 'Create Database',
            self::MIGRATE_DATABASE => 'Migrate Database',
            self::SEED_DATABASE => 'Seed Database',
            self::CREATE_ADMIN => 'Create Admin User',
            self::HEALTH_CHECK => 'Health Check',
            self::COMPLETE => 'Complete',
        };
    }
}
