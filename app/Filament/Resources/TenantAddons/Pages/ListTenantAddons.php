<?php

namespace App\Filament\Resources\TenantAddons\Pages;

use App\Filament\Resources\TenantAddons\TenantAddonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantAddons extends ListRecords
{
    protected static string $resource = TenantAddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
