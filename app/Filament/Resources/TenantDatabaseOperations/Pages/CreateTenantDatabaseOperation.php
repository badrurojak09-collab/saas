<?php

namespace App\Filament\Resources\TenantDatabaseOperations\Pages;

use App\Filament\Resources\TenantDatabaseOperations\TenantDatabaseOperationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantDatabaseOperation extends CreateRecord
{
    protected static string $resource = TenantDatabaseOperationResource::class;
}
