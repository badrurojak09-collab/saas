<?php

namespace App\Filament\Tenant\Resources\CurriculumResource\Pages;

use App\Filament\Tenant\Resources\CurriculumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCurriculums extends ListRecords
{
    protected static string $resource = CurriculumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
