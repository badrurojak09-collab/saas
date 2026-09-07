<?php

namespace App\Filament\Resources\TenantMigrationVersions\Pages;

use App\Filament\Resources\TenantMigrationVersions\TenantMigrationVersionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantMigrationVersion extends CreateRecord
{
    protected static string $resource = TenantMigrationVersionResource::class;
}
