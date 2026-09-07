<?php

namespace App\Filament\Tenant\Resources\LecturerResource\Pages;

use App\Filament\Tenant\Resources\LecturerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLecturer extends EditRecord
{
    protected static string $resource = LecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
