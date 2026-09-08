<?php

namespace App\Providers;

use App\Models\Landlord\Addon;
use App\Models\Landlord\Package;
use App\Models\Tenant\User;
use App\Policies\Landlord\AddonPolicy;
use App\Policies\Landlord\PackagePolicy;
use App\Models\Tenant\OrganizationMembership;
use App\Models\Tenant\OrganizationUnit;
use App\Policies\Tenant\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Tenancy services are registered in TenancyServiceProvider
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Mendaftarkan Policy Landlord & Tenant ke Gate Laravel
        Gate::policy(Addon::class, AddonPolicy::class);
        Gate::policy(Package::class, PackagePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(OrganizationUnit::class, 'App\\Policies\\Tenant\\OrganizationUnitPolicy');
        Gate::policy(OrganizationMembership::class, 'App\\Policies\\Tenant\\OrganizationMembershipPolicy');

        // 2. Mendaftarkan folder migrasi landlord agar terdeteksi otomatis
        if (file_exists(database_path('migrations/landlord'))) {
            $this->loadMigrationsFrom(database_path('migrations/landlord'));
        }

        // 3. Mendaftarkan Observers untuk Organization Domain
        \App\Models\Tenant\OrganizationUnit::observe([
            \App\Observers\Tenant\OrganizationUnitObserver::class,
        ]);
        \App\Models\Tenant\OrganizationMembership::observe([
            \App\Observers\Tenant\OrganizationMembershipObserver::class,
        ]);
    }
}
