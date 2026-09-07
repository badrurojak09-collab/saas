<?php

namespace App\Filament\Resources\TenantDatabaseOperations\Pages;

use App\Filament\Resources\TenantDatabaseOperations\TenantDatabaseOperationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantDatabaseOperation extends EditRecord
{
    protected static string $resource = TenantDatabaseOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
