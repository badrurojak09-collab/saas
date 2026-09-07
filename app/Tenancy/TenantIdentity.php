<?php

namespace App\Tenancy;

use App\Contracts\Tenancy\IdentifiesTenant;

final class TenantIdentity implements IdentifiesTenant
{
    public function __construct(
        private readonly string $id,
        private readonly ?string $database = null,
    ) {}

    public function getTenantKey(): string
    {
        return $this->id;
    }

    public function getDatabaseName(): ?string
    {
        return $this->database;
    }
}
