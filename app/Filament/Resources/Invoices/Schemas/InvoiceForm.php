<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\Landlord\InvoiceStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Header')
                    ->description('Tenant billing document reference')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('tenant_id')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('invoice_number')
                                    ->label('Invoice No.')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('INV-2025-001'),
                                Select::make('status')
                                    ->options(InvoiceStatus::class)
                                    ->default(InvoiceStatus::DRAFT)
                                    ->required(),
                                TextInput::make('currency')
                                    ->default('IDR')
                                    ->maxLength(3)
                                    ->required(),
                                DateTimePicker::make('issued_at'),
                                DateTimePicker::make('due_at'),
                                DateTimePicker::make('paid_at'),
                            ]),
                    ])
                    ->ColumnSpan(2),

                Section::make('Amounts')
                    ->description('Invoice totals and tax breakdown')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0.0)
                                    ->required(),
                                TextInput::make('discount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0.0)
                                    ->required(),
                                TextInput::make('tax')
                                    ->label('Tax (PPN)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0.0)
                                    ->required(),
                                TextInput::make('total')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0.0)
                                    ->required(),
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
