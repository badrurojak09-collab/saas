<?php

namespace App\Filament\Resources\TenantDatabases\Pages;

use App\Filament\Resources\TenantDatabases\TenantDatabaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantDatabases extends ListRecords
{
    protected static string $resource = TenantDatabaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
