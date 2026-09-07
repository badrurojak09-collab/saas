<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\Tenant\TenantAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AuthController extends Controller
{
    public function __construct(
        protected TenantAuthenticationService $authService,
    ) {}

    public function create(): View
    {
        return view('tenant.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            $success = $this->authService->attempt(
                $credentials['email'],
                $credentials['password'],
                $request->boolean('remember'),
                $request
            );

            if (! $success) {
                return back()->withErrors(['email' => 'Email atau password tidak valid.'])->onlyInput('email');
            }
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->onlyInput('email');
        }

        return redirect()->intended(route('tenant.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('tenant.login');
    }
}
