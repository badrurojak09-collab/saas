<?php

namespace App\Filament\Tenant\Resources\StudyPlanResource\Pages;

use App\Filament\Tenant\Resources\StudyPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudyPlan extends ViewRecord
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
