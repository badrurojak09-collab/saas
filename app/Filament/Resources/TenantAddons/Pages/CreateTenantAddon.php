<?php

namespace App\Filament\Resources\TenantAddons\Pages;

use App\Filament\Resources\TenantAddons\TenantAddonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantAddon extends CreateRecord
{
    protected static string $resource = TenantAddonResource::class;
}
