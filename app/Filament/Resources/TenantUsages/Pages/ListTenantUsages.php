<?php

namespace App\Filament\Resources\TenantUsages\Pages;

use App\Filament\Resources\TenantUsages\TenantUsageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantUsages extends ListRecords
{
    protected static string $resource = TenantUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
