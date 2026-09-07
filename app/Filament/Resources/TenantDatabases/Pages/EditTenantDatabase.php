<?php

namespace App\Filament\Resources\TenantDatabases\Pages;

use App\Filament\Resources\TenantDatabases\TenantDatabaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantDatabase extends EditRecord
{
    protected static string $resource = TenantDatabaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
