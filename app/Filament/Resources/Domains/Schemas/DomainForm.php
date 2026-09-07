<?php

namespace App\Filament\Resources\Domains\Schemas;

use App\Enums\Landlord\DomainType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DomainForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Domain Configuration')
                    ->description('Domain routing for tenant')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('tenant_id')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('domain')
                                    ->label('Domain Host')
                                    ->placeholder('kampus.siakad.id or sia.kampus.ac.id')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Select::make('type')
                                    ->options(DomainType::class)
                                    ->default(DomainType::SUBDOMAIN)
                                    ->required(),
                                Select::make('ssl_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'active' => 'Active',
                                        'failed' => 'Failed',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Verification & Primary Flags')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Toggle::make('is_primary')
                                    ->label('Primary Tenant Domain')
                                    ->default(false),
                                Toggle::make('is_verified')
                                    ->label('Domain Verified')
                                    ->default(false),
                                DateTimePicker::make('verified_at'),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Metadata')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('metadata'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
