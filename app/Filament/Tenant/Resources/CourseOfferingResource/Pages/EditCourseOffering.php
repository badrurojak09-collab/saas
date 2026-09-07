<?php

namespace App\Filament\Tenant\Resources\CourseOfferingResource\Pages;

use App\Filament\Tenant\Resources\CourseOfferingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseOffering extends EditRecord
{
    protected static string $resource = CourseOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
