<?php

namespace App\Filament\Resources\TenantDatabases\Pages;

use App\Filament\Resources\TenantDatabases\TenantDatabaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantDatabase extends CreateRecord
{
    protected static string $resource = TenantDatabaseResource::class;
}
