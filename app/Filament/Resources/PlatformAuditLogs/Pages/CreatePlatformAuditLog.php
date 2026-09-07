<?php

namespace App\Filament\Resources\PlatformAuditLogs\Pages;

use App\Filament\Resources\PlatformAuditLogs\PlatformAuditLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlatformAuditLog extends CreateRecord
{
    protected static string $resource = PlatformAuditLogResource::class;
}
