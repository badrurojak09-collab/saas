<?php

namespace App\Contracts\Tenancy;

interface IdentifiesTenant
{
    public function getTenantKey(): string;

    public function getDatabaseName(): ?string;
}
