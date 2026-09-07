<?php

namespace App\Actions\Tenant;

use App\Events\Tenant\TenantUserDeleted;
use App\Models\Tenant\User;
use App\Tenancy\Contracts\TenantManager;

class DeleteTenantUserAction
{
    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    public function execute(User $user, ?string $performedBy = null): void
    {
        $tenantId = (string) $this->tenantManager->requireCurrent()->getKey();
        $userId = (string) $user->id;

        $user->delete();

        event(new TenantUserDeleted($tenantId, $userId, $performedBy));
    }
}
