<?php

namespace App\Filament\Resources\TenantAddons\Pages;

use App\Filament\Resources\TenantAddons\TenantAddonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantAddon extends EditRecord
{
    protected static string $resource = TenantAddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
