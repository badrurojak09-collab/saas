<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\AuthenticateTenantUserAction;
use App\Models\Tenant\User;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Exceptions\TenantContextMissingException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantAuthenticationService
{
    public function __construct(
        protected AuthenticateTenantUserAction $authenticateAction,
        protected TenantManager $tenantManager,
    ) {}

    /**
     * Attempt to authenticate a tenant user.
     *
     * @throws TenantContextMissingException
     */
    public function attempt(
        string $login,
        #[\SensitiveParameter] string $password,
        bool $remember = false,
        ?Request $request = null
    ): bool {
        return $this->authenticateAction->execute($login, $password, $remember, $request);
    }

    /**
     * Log out current tenant user and invalidate session.
     */
    public function logout(?Request $request = null): void
    {
        Auth::guard('tenant')->logout();

        $req = $request ?? (request()->hasSession() ? request() : null);
        if ($req && $req->hasSession()) {
            $req->session()->invalidate();
            $req->session()->regenerateToken();
        }
    }

    /**
     * Retrieve the currently authenticated tenant user.
     */
    public function user(): ?User
    {
        /** @var User|null $user */
        $user = Auth::guard('tenant')->user();

        return $user;
    }

    /**
     * Check if a tenant user is currently authenticated.
     */
    public function check(): bool
    {
        return Auth::guard('tenant')->check();
    }
}
