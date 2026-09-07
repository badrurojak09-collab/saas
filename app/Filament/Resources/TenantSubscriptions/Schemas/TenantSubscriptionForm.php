<?php

namespace App\Filament\Resources\TenantSubscriptions\Schemas;

use App\Enums\Landlord\SubscriptionStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription Assignment')
                    ->description('Tenant and plan tier association')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('tenant_id')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('package_id')
                                    ->relationship('package', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('status')
                                    ->options(SubscriptionStatus::class)
                                    ->default(SubscriptionStatus::TRIAL)
                                    ->required(),
                                Toggle::make('auto_renew')
                                    ->label('Auto-Renew Plan')
                                    ->default(true),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Pricing & Terms')
                    ->description('Billing period and customized rates')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('price')
                                    ->label('Charged Price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0.0)
                                    ->required(),
                                TextInput::make('currency')
                                    ->default('IDR')
                                    ->required(),
                                DateTimePicker::make('trial_ends_at')
                                    ->label('Trial Expiry'),
                                DateTimePicker::make('starts_at')
                                    ->label('Effective Start')
                                    ->required()
                                    ->default(now()),
                                DateTimePicker::make('ends_at')
                                    ->label('Subscription Expiry'),
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
