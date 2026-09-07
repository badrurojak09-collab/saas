<?php

namespace App\Filament\Tenant\Resources\StudyPlanResource\Pages;

use App\Filament\Tenant\Resources\StudyPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudyPlan extends EditRecord
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
