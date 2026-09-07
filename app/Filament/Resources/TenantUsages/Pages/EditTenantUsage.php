<?php

namespace App\Filament\Resources\TenantUsages\Pages;

use App\Filament\Resources\TenantUsages\TenantUsageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantUsage extends EditRecord
{
    protected static string $resource = TenantUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
