<?php

namespace App\Filament\Resources\TenantMigrationVersions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TenantMigrationVersionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_database_id')
                    ->relationship('tenantDatabase', 'name')
                    ->required(),
                TextInput::make('migration')
                    ->required(),
                TextInput::make('batch')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('applied_at')
                    ->required(),
                TextInput::make('execution_time_ms')
                    ->numeric(),
            ]);
    }
}
