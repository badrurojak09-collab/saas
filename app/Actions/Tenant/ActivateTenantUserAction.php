<?php

namespace App\Actions\Tenant;

use App\Enums\Tenant\UserStatus;
use App\Events\Tenant\TenantUserActivated;
use App\Models\Tenant\User;
use App\Tenancy\Contracts\TenantManager;

class ActivateTenantUserAction
{
    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    public function execute(User $user, ?string $performedBy = null): User
    {
        $tenantId = (string) $this->tenantManager->requireCurrent()->getKey();

        $user->update(['status' => UserStatus::Active]);

        event(new TenantUserActivated($tenantId, (string) $user->id, $performedBy));

        return $user->fresh();
    }
}
