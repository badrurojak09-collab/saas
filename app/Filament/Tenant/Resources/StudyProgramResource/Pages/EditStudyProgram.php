<?php

namespace App\Filament\Tenant\Resources\StudyProgramResource\Pages;

use App\Filament\Tenant\Resources\StudyProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudyProgram extends EditRecord
{
    protected static string $resource = StudyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
