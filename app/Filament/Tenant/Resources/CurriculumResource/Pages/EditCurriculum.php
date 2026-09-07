<?php

namespace App\Filament\Tenant\Resources\CurriculumResource\Pages;

use App\Filament\Tenant\Resources\CurriculumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCurriculum extends EditRecord
{
    protected static string $resource = CurriculumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
