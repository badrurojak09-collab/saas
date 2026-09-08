<?php

namespace App\Providers;

use App\Auth\Tenant\TenantAuthManager;
use App\Console\Commands\TenantConnectionTestCommand;
use App\Console\Commands\TenantCurrentCommand;
use App\Console\Commands\TenantProvisionCommand;
use App\Console\Commands\TenantProvisionRetryCommand;
use App\Console\Commands\TenantProvisionStatusCommand;
use App\Console\Commands\TenantSeedPermissionsCommand;
use App\Console\Commands\TenantSwitchCommand;
use App\Contracts\Tenancy\TenantResolver as LegacyTenantResolverContract;
use App\Events\Landlord\TenantCreated;
use App\Listeners\Landlord\DispatchTenantProvisioning;
use App\Services\Tenant\TenantAdminProvisioningService;
use App\Services\Tenant\TenantAuthenticationService;
use App\Services\Tenant\TenantDatabaseService;
use App\Services\Tenant\TenantHealthCheckService;
use App\Services\Tenant\TenantMigrationService;
use App\Services\Tenant\TenantPermissionService;
use App\Services\Tenant\TenantProvisioningService;
use App\Services\Tenant\TenantSeederService;
use App\Services\Tenant\TenantUserService;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;
use App\Tenancy\Contracts\TenantConnectionManager as TenantConnectionManagerContract;
use App\Tenancy\Contracts\TenantContext as TenantContextContract;
use App\Tenancy\Contracts\TenantManager as TenantManagerContract;
use App\Tenancy\Contracts\TenantResolver as TenantResolverContract;
use App\Tenancy\Middleware\InitializeTenant;
use App\Tenancy\Middleware\PreventTenantLeakage;
use App\Tenancy\Middleware\ResolveTenant;
use App\Tenancy\Provisioning\TenantProvisioningContext;
use App\Tenancy\Provisioning\TenantProvisioningLock;
use App\Tenancy\Services\TenantManagerService;
use App\Tenancy\Services\TenantResolverService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * Register tenancy services and singletons.
     */
    public function register(): void
    {
        // Bind Context
        $this->app->singleton(TenantContextContract::class, TenantContext::class);
        $this->app->singleton(TenantContext::class);

        // Bind Connection Manager
        $this->app->singleton(TenantConnectionManagerContract::class, TenantConnectionManager::class);
        $this->app->singleton(TenantConnectionManager::class);

        // Bind Database Service
        $this->app->singleton(TenantDatabaseService::class);

        // Bind Tenant Resolver
        $this->app->singleton(TenantResolverContract::class, TenantResolverService::class);
        $this->app->singleton(TenantResolverService::class);
        $this->app->alias(TenantResolverContract::class, LegacyTenantResolverContract::class);

        // Bind Tenant Manager
        $this->app->singleton(TenantManagerContract::class, TenantManagerService::class);
        $this->app->singleton(TenantManagerService::class);

        // Bind Provisioning Services
        $this->app->singleton(TenantProvisioningLock::class);
        $this->app->singleton(TenantProvisioningContext::class);
        $this->app->singleton(TenantMigrationService::class);
        $this->app->singleton(TenantSeederService::class);
        $this->app->singleton(TenantAdminProvisioningService::class);
        $this->app->singleton(TenantHealthCheckService::class);
        $this->app->singleton(TenantProvisioningService::class);

        // Bind Identity & Auth Services
        $this->app->singleton(TenantUserService::class);
        $this->app->singleton(TenantPermissionService::class);
        $this->app->singleton(TenantAuthenticationService::class);
        $this->app->singleton(TenantAuthManager::class);
    }

    /**
     * Bootstrap tenancy services, event listeners, and CLI commands.
     */
    public function boot(): void
    {
        Livewire::addPersistentMiddleware([
            ResolveTenant::class,
            InitializeTenant::class,
            PreventTenantLeakage::class,
        ]);

        // Register Event Listeners
        Event::listen(TenantCreated::class, DispatchTenantProvisioning::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                TenantCurrentCommand::class,
                TenantSwitchCommand::class,
                TenantConnectionTestCommand::class,
                TenantProvisionCommand::class,
                TenantProvisionRetryCommand::class,
                TenantProvisionStatusCommand::class,
                TenantSeedPermissionsCommand::class,
            ]);
        }
    }
}
