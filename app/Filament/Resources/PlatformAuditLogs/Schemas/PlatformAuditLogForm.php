<?php

namespace App\Filament\Resources\PlatformAuditLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlatformAuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name'),
                Select::make('actor_id')
                    ->relationship('actor', 'name'),
                TextInput::make('action')
                    ->required(),
                TextInput::make('entity_type')
                    ->required(),
                TextInput::make('entity_id'),
                TextInput::make('old_values'),
                TextInput::make('new_values'),
                TextInput::make('ip_address'),
                Textarea::make('user_agent')
                    ->columnSpanFull(),
                TextInput::make('request_id'),
            ]);
    }
}
