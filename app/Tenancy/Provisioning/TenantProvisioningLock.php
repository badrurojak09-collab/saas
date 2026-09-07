<?php

namespace App\Tenancy\Provisioning;

use App\Tenancy\Exceptions\Provisioning\TenantProvisioningAlreadyRunningException;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;

class TenantProvisioningLock
{
    public const DEFAULT_TTL_SECONDS = 300;

    /**
     * @var array<string, Lock>
     */
    private array $locks = [];

    /**
     * Acquire a distributed lock for tenant provisioning.
     *
     * @throws TenantProvisioningAlreadyRunningException
     */
    public function acquire(string $tenantId, int $seconds = self::DEFAULT_TTL_SECONDS): Lock
    {
        $lockKey = $this->getLockKey($tenantId);
        $lock = Cache::lock($lockKey, $seconds);

        if (! $lock->get()) {
            throw new TenantProvisioningAlreadyRunningException(
                sprintf('Tenant [%s] is already being provisioned.', $tenantId)
            );
        }

        $this->locks[$tenantId] = $lock;

        return $lock;
    }

    /**
     * Release the distributed lock for tenant provisioning.
     */
    public function release(string $tenantId): void
    {
        if (isset($this->locks[$tenantId])) {
            $this->locks[$tenantId]->release();
            unset($this->locks[$tenantId]);

            return;
        }

        $lockKey = $this->getLockKey($tenantId);
        Cache::lock($lockKey)->forceRelease();
    }

    /**
     * Check if tenant provisioning is currently locked.
     */
    public function isLocked(string $tenantId): bool
    {
        $lockKey = $this->getLockKey($tenantId);
        $lock = Cache::lock($lockKey, 1);
        $acquired = $lock->get();

        if ($acquired) {
            $lock->release();

            return false;
        }

        return true;
    }

    /**
     * Run a callback under the tenant provisioning lock.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     *
     * @throws TenantProvisioningAlreadyRunningException
     */
    public function run(string $tenantId, callable $callback, int $seconds = self::DEFAULT_TTL_SECONDS): mixed
    {
        $this->acquire($tenantId, $seconds);

        try {
            return $callback();
        } finally {
            $this->release($tenantId);
        }
    }

    protected function getLockKey(string $tenantId): string
    {
        return 'tenant-provision:'.$tenantId;
    }
}
