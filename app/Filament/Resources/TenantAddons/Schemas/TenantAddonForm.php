<?php

namespace App\Filament\Resources\TenantAddons\Schemas;

use App\Enums\Landlord\AddonStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantAddonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tenant Addon Association')
                    ->description('Installed module on campus instance')
                    ->icon('heroicon-o-squares-plus')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('tenant_id')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('addon_id')
                                    ->relationship('addon', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('status')
                                    ->options(AddonStatus::class)
                                    ->default(AddonStatus::ACTIVE)
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Price Charged')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0.0)
                                    ->required(),
                                DateTimePicker::make('starts_at')
                                    ->required()
                                    ->default(now()),
                                DateTimePicker::make('ends_at'),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Metadata')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('metadata'),
                    ])
                    ->ColumnSpanFull(),
            ]);
    }
}
