<?php

namespace App\Filament\Tenant\Resources\StudyPlanResource\Pages;

use App\Filament\Tenant\Resources\StudyPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudyPlans extends ListRecords
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
