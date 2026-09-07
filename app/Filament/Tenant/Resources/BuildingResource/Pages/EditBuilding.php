<?php

namespace App\Filament\Tenant\Resources\BuildingResource\Pages;

use App\Filament\Tenant\Resources\BuildingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBuilding extends EditRecord
{
    protected static string $resource = BuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
