<?php

namespace App\Filament\Resources\PlatformUsers\Schemas;

use App\Enums\Landlord\PlatformUserStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlatformUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('status')
                    ->options(PlatformUserStatus::class)
                    ->default('active')
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                DateTimePicker::make('last_login_at'),
                TextInput::make('metadata'),
            ]);
    }
}
