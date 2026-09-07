<?php

namespace App\Filament\Resources\TenantDatabaseOperations\Pages;

use App\Filament\Resources\TenantDatabaseOperations\TenantDatabaseOperationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantDatabaseOperations extends ListRecords
{
    protected static string $resource = TenantDatabaseOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
