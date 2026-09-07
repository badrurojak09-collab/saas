<?php

namespace App\Events\Landlord;

use App\DTOs\Tenant\TenantAdminData;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenantCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $tenantId,
        public readonly ?TenantAdminData $adminData = null,
    ) {}
}
