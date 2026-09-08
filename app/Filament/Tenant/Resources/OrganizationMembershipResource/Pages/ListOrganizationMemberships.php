<?php

namespace App\Filament\Tenant\Resources\OrganizationMembershipResource\Pages;

use App\Filament\Tenant\Resources\OrganizationMembershipResource;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationMemberships extends ListRecords
{
    protected static string $resource = OrganizationMembershipResource::class;
}
