<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Enums\Landlord\BillingCycle;
use App\Enums\Landlord\PackageStatus;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Package Information')
                    ->description('Plan tier and pricing definitions')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('code')
                                    ->label('Package Code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                                TextInput::make('name')
                                    ->label('Package Name')
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
                                    ->options(PackageStatus::class)
                                    ->default(PackageStatus::Active)
                                    ->required(),
                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                                Textarea::make('description')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Usage Limits & Quotas')
                    ->description('Define resource limits (e.g., max_students, max_storage_gb, max_lecturers)')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        KeyValue::make('limits')
                            ->keyLabel('Limit Key')
                            ->valueLabel('Value'),
                    ])
                    ->ColumnSpanFull(),

                Section::make('Metadata')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('metadata'),
                    ])
                    ->ColumnSpanFull(),
            ]);
    }
}
