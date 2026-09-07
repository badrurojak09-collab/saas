<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\Landlord\PaymentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Payment Header')
                    ->description('Payment document reference')
                    ->columns(2)
                    ->schema([

                        Select::make('invoice_id')
                            ->relationship('invoice', 'id')
                            ->required(),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('currency')
                            ->required()
                            ->default('IDR'),
                        TextInput::make('method')
                            ->required(),
                        Select::make('status')
                            ->options(PaymentStatus::class)
                            ->default('pending')
                            ->required(),
                        DateTimePicker::make('paid_at'),
                        TextInput::make('reference'),
                        KeyValue::make('metadata')
                            ->ColumnSpanFull(),
                    ])
                    ->ColumnSpanFull()


            ]);
    }
}
