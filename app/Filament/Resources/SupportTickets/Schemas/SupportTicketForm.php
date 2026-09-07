<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use App\Enums\Landlord\SupportTicketPriority;
use App\Enums\Landlord\SupportTicketStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('ticket_number')
                    ->required(),
                TextInput::make('subject')
                    ->required(),
                Select::make('priority')
                    ->options(SupportTicketPriority::class)
                    ->required(),
                Select::make('status')
                    ->options(SupportTicketStatus::class)
                    ->required(),
                TextInput::make('created_by')
                    ->required(),
                TextInput::make('assigned_to'),
                DateTimePicker::make('resolved_at'),
                TextInput::make('metadata'),
            ]);
    }
}
