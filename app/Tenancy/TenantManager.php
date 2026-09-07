<?php

namespace App\Tenancy;

use App\Contracts\Tenancy\IdentifiesTenant;

final class TenantManager
{
    public function __construct(
        private readonly TenantContext $context,
        private readonly TenantConnectionManager $connections,
    ) {}

    public function initialize(IdentifiesTenant $tenant, bool $connect = false): void
    {
        $this->end();

        $this->context->set($tenant);
        $this->connections->setDatabase($tenant->getDatabaseName());

        if ($connect) {
            $this->connections->reconnect();
        }
    }

    public function current(): IdentifiesTenant
    {
        return $this->context->current();
    }

    public function has(): bool
    {
        return $this->context->has();
    }

    public function end(): void
    {
        $this->connections->disconnect();
        $this->context->clear();
    }
}
