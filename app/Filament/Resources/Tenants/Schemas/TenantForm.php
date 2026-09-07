<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Enums\Landlord\TenantStatus;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tenant Identity')
                    ->description('Basic institutional identification')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Campus / Institution Name')
                                    ->required()
                                    ->maxLength(200),
                                TextInput::make('legal_name')
                                    ->label('Legal Entity Name')
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->label('Tenant Code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                                TextInput::make('slug')
                                    ->label('Slug / Subdomain Key')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Regional & Status')
                    ->description('Status, timezone, and localization configuration')
                    ->icon('heroicon-o-globe-asia-australia')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('status')
                                    ->options(TenantStatus::class)
                                    ->default(TenantStatus::PROVISIONING)
                                    ->required(),
                                TextInput::make('timezone')
                                    ->required()
                                    ->default('Asia/Jakarta'),
                                TextInput::make('locale')
                                    ->required()
                                    ->default('id'),
                                TextInput::make('country')
                                    ->required()
                                    ->default('ID'),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Metadata & Custom Attributes')
                    ->description('Arbitrary JSON metadata for campus configurations')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('metadata')
                            ->keyLabel('Attribute')
                            ->valueLabel('Value'),
                    ])
                    ->ColumnSpan('full'),
            ]);
    }
}
