<?php

namespace App\Filament\Tenant\Resources\FacultyResource\Pages;

use App\Filament\Tenant\Resources\FacultyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaculty extends EditRecord
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
