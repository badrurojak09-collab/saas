<?php

namespace App\Filament\Resources\TenantMigrationVersions\Pages;

use App\Filament\Resources\TenantMigrationVersions\TenantMigrationVersionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantMigrationVersion extends EditRecord
{
    protected static string $resource = TenantMigrationVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
