<?php

namespace App\Actions\Tenant;

use App\Enums\Tenant\UserStatus;
use App\Events\Tenant\TenantUserSuspended;
use App\Models\Tenant\User;
use App\Tenancy\Contracts\TenantManager;

class SuspendTenantUserAction
{
    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    public function execute(User $user, ?string $performedBy = null): User
    {
        $tenantId = (string) $this->tenantManager->requireCurrent()->getKey();

        $user->update(['status' => UserStatus::Suspended]);

        event(new TenantUserSuspended($tenantId, (string) $user->id, $performedBy));

        return $user->fresh();
    }
}
