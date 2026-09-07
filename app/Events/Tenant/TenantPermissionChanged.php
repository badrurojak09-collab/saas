<?php

namespace App\Events\Tenant;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenantPermissionChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $tenantId,
        public readonly string $modelType,
        public readonly string $modelId,
        public readonly string $action,
        public readonly string|array $permission,
        public readonly ?string $performedBy = null,
    ) {}
}
