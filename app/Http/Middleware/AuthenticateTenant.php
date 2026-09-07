<?php

namespace App\Http\Middleware;

use App\Enums\Landlord\TenantStatus;
use App\Enums\Tenant\UserStatus;
use App\Models\Tenant\User;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Exceptions\TenantContextMissingException;
use App\Tenancy\Exceptions\TenantInactiveException;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTenant
{
    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    /**
     * @throws AuthenticationException
     * @throws TenantContextMissingException
     * @throws TenantInactiveException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->tenantManager->isInitialized()) {
            throw new TenantContextMissingException('Cannot access tenant protected route without active tenant context.');
        }

        $tenant = $this->tenantManager->requireCurrent();
        if ($tenant->status !== TenantStatus::ACTIVE) {
            throw new TenantInactiveException("Tenant [{$tenant->code}] is inactive or suspended.");
        }

        if (! Auth::guard('tenant')->check()) {
            throw new AuthenticationException('Unauthenticated.', ['tenant']);
        }

        /** @var User $user */
        $user = Auth::guard('tenant')->user();

        if ($user->status !== UserStatus::Active) {
            Auth::guard('tenant')->logout();
            abort(403, 'Account is inactive or suspended.');
        }

        return $next($request);
    }
}
