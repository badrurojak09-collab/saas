<?php

namespace App\Filament\Resources\TenantDatabases\Schemas;

use App\Enums\Landlord\TenantDatabaseStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantDatabaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tenant & Connection')
                    ->description('Database host, port, and tenant assignment')
                    ->icon('heroicon-o-server')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('tenant_id')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('name')
                                    ->label('Connection Identifier')
                                    ->default('primary')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('driver')
                                    ->default('mysql')
                                    ->required()
                                    ->maxLength(30),
                                Toggle::make('is_primary')
                                    ->label('Primary Connection for Tenant')
                                    ->default(true),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Credentials & Topology')
                    ->description('Database name, host location, and credentials')
                    ->icon('heroicon-o-circle-stack')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('host')
                                    ->required()
                                    ->default('127.0.0.1'),
                                TextInput::make('port')
                                    ->required()
                                    ->numeric()
                                    ->default(3306),
                                TextInput::make('database')
                                    ->label('Database Name')
                                    ->required(),
                                TextInput::make('username')
                                    ->required()
                                    ->default('root'),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable(),
                                Select::make('status')
                                    ->options(TenantDatabaseStatus::class)
                                    ->default(TenantDatabaseStatus::CREATING)
                                    ->required(),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('State & Lifecycle')
                    ->description('Migration and backup tracking')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('schema_version')
                                    ->placeholder('v1.0.0'),
                                DateTimePicker::make('last_migrated_at'),
                                DateTimePicker::make('last_backup_at'),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Metadata')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('metadata'),
                    ])
                    ->ColumnSpan('full'),
            ]);
    }
}
