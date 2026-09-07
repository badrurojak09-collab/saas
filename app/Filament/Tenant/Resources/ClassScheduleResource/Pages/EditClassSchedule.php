<?php

namespace App\Filament\Tenant\Resources\ClassScheduleResource\Pages;

use App\Filament\Tenant\Resources\ClassScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassSchedule extends EditRecord
{
    protected static string $resource = ClassScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
