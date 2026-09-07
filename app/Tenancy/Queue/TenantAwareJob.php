<?php

namespace App\Tenancy\Queue;

trait TenantAwareJob
{
    /**
     * The ID of the tenant this job belongs to.
     */
    public string $tenantId;

    /**
     * Assign the tenant ID to the job.
     */
    public function forTenant(string $tenantId): static
    {
        $this->tenantId = $tenantId;

        return $this;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            new InitializeTenantForJob,
        ];
    }
}
