<?php

namespace App\Filament\Resources\ProvisioningJobs\Schemas;

use App\Enums\Landlord\ProvisioningJobStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

class ProvisioningJobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Header')
                    ->description('Payment document reference')
                    ->columns(2) // 👈 Menjadikan isi section membagi 2 kolom
                    ->schema([
                        Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required(),

                        TextInput::make('job_type')
                            ->required(),

                        Select::make('status')
                            ->options(ProvisioningJobStatus::class)
                            ->required(),

                        TextInput::make('attempts')
                            ->required()
                            ->numeric()
                            ->default(0),

                        DateTimePicker::make('started_at'),

                        DateTimePicker::make('completed_at'),

                        // Bikin Textarea & Payload memenuhi 2 kolom penuh agar estetik
                        Textarea::make('error_message')
                            ->columnSpanFull(),

                        KeyValue::make('payload')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
