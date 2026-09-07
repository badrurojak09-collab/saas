<?php

namespace App\Actions\Tenant;

use App\Enums\Landlord\TenantStatus;
use App\Enums\Tenant\UserStatus;
use App\Models\Tenant\User;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Exceptions\TenantContextMissingException;
use App\Tenancy\Exceptions\TenantInactiveException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticateTenantUserAction
{
    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    /**
     * @throws TenantContextMissingException
     * @throws TenantInactiveException
     * @throws ValidationException
     */
    public function execute(
        string $login,
        #[\SensitiveParameter] string $password,
        bool $remember = false,
        ?Request $request = null,
        int $maxAttempts = 5,
        int $decaySeconds = 60,
    ): bool {
        if (! $this->tenantManager->isInitialized()) {
            throw new TenantContextMissingException('Cannot authenticate tenant user without active tenant context.');
        }

        $tenant = $this->tenantManager->requireCurrent();
        if ($tenant->status !== TenantStatus::ACTIVE) {
            throw new TenantInactiveException("Tenant [{$tenant->code}] is inactive or suspended.");
        }

        $tenantId = (string) $tenant->getKey();
        $ip = $request?->ip() ?? request()->ip() ?? '127.0.0.1';
        $throttleKey = "tenant-login:{$tenantId}:{$login}:{$ip}";

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => [trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ])],
            ]);
        }

        // Search user by email or username on tenant connection
        /** @var User|null $user */
        $user = User::query()
            ->where(function ($query) use ($login) {
                $query->where('email', $login)
                    ->orWhere('username', $login);
            })
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, $decaySeconds);

            return false;
        }

        // Validate user status
        if ($user->status !== UserStatus::Active) {
            RateLimiter::hit($throttleKey, $decaySeconds);

            return false;
        }

        // Clear throttle on success
        RateLimiter::clear($throttleKey);

        // Update login audit info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);

        // Login via tenant guard
        Auth::guard('tenant')->login($user, $remember);

        // Regenerate session if request is available
        $req = $request ?? (request()->hasSession() ? request() : null);
        if ($req && $req->hasSession()) {
            $req->session()->regenerate();
        }

        return true;
    }
}
