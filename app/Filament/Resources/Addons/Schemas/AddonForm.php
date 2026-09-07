<?php

namespace App\Filament\Resources\Addons\Schemas;

use App\Enums\Landlord\AddonStatus;
use App\Enums\Landlord\BillingCycle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AddonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Addon Overview')
                    ->description('Modular extension / plugin details')
                    ->icon('heroicon-o-puzzle-piece')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('code')
                                    ->label('Addon Code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                                TextInput::make('name')
                                    ->label('Addon Name')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('price')
                                    ->label('Price (IDR)')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0.0),
                                Select::make('billing_cycle')
                                    ->options(BillingCycle::class)
                                    ->default(BillingCycle::MONTHLY)
                                    ->required(),
                                Select::make('status')
                                    ->options(AddonStatus::class)
                                    ->default(AddonStatus::ACTIVE)
                                    ->required(),
                                Textarea::make('description')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Configuration Parameters')
                    ->description('Module options and feature keys')
                    ->icon('heroicon-o-adjustments-vertical')
                    ->schema([
                        KeyValue::make('config')
                            ->keyLabel('Parameter')
                            ->valueLabel('Value'),
                    ])
                    ->ColumnSpanFull(),
            ]);
    }
}
