<?php

namespace App\Auth\Tenant;

use App\Models\Tenant\User;
use App\Services\Tenant\TenantAuthenticationService;
use App\Tenancy\Contracts\TenantManager;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class TenantAuthManager
{
    public function __construct(
        protected TenantAuthenticationService $authService,
        protected TenantManager $tenantManager,
    ) {}

    public function guard(): Guard
    {
        return Auth::guard('tenant');
    }

    public function user(): ?User
    {
        return $this->authService->user();
    }

    public function check(): bool
    {
        return $this->authService->check();
    }

    public function id(): ?string
    {
        return $this->user()?->id;
    }
}
