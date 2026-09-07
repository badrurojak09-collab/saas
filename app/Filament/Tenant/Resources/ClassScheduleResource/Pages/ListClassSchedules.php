<?php

namespace App\Filament\Tenant\Resources\ClassScheduleResource\Pages;

use App\Filament\Tenant\Resources\ClassScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassSchedules extends ListRecords
{
    protected static string $resource = ClassScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
