<?php

namespace App\Filament\Resources\TenantMigrationVersions\Pages;

use App\Filament\Resources\TenantMigrationVersions\TenantMigrationVersionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantMigrationVersions extends ListRecords
{
    protected static string $resource = TenantMigrationVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
