<?php

use App\Http\Controllers\HealthController;
use App\Http\Middleware\AuthenticateTenant;
use App\Http\Middleware\InitializeTenant;
use App\Tenancy\Middleware\InitializeLivewireTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::get('/health', HealthController::class)
                ->name('health');
            // Route::middleware('web')->group(base_path('routes/tenant.php'));
            Route::middleware(['web', 'tenant'])->group(base_path('routes/tenant.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(InitializeLivewireTenant::class);

        $middleware->alias([
            'tenant' => InitializeTenant::class,
            'tenant.auth' => AuthenticateTenant::class,
            'auth.tenant' => AuthenticateTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->report(function (Throwable $exception): void {
            if (app()->environment('local')) {
                Log::channel('single')->error('Local exception diagnostic', [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                ]);
            }
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (app()->environment('local') && $request->is('livewire-*/*', '/tenant/*', '/tenant')) {
                return response()->json([
                    'error' => $exception::class,
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ], 500);
            }
        });
    })
    ->create();
