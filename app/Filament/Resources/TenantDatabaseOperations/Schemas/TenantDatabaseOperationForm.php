<?php

namespace App\Filament\Resources\TenantDatabaseOperations\Schemas;

use App\Enums\Landlord\DatabaseOperationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TenantDatabaseOperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_database_id')
                    ->relationship('tenantDatabase', 'name')
                    ->required(),
                TextInput::make('operation')
                    ->required(),
                Select::make('status')
                    ->options(DatabaseOperationStatus::class)
                    ->required(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                Textarea::make('error_message')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
            ]);
    }
}
