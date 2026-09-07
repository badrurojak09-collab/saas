<?php

namespace App\Filament\Tenant\Resources\SemesterResource\Pages;

use App\Filament\Tenant\Resources\SemesterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSemester extends EditRecord
{
    protected static string $resource = SemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
