<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Enums\Landlord\AnnouncementStatus;
use App\Enums\Landlord\AnnouncementType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->options(AnnouncementType::class)
                    ->required(),
                Select::make('status')
                    ->options(AnnouncementStatus::class)
                    ->required(),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('expires_at'),
                TextInput::make('created_by')
                    ->required(),
            ]);
    }
}
