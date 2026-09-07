<?php

namespace App\Actions\Landlord;

use App\DTOs\Tenant\ProvisionTenantData;
use App\DTOs\Tenant\TenantAdminData;
use App\Enums\Landlord\DomainType;
use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Events\Landlord\TenantCreated;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateTenantAction
{
    /**
     * Creates the landlord control-plane records and fires TenantCreated event.
     * Tenant users and physical database belong to the tenant provisioning engine.
     *
     * @param  array<string, mixed>|ProvisionTenantData  $data
     */
    public function execute(array|ProvisionTenantData $data): Tenant
    {
        if ($data instanceof ProvisionTenantData) {
            $name = trim($data->name);
            $slug = Str::slug($data->slug ?: $name);
            $rawCode = $data->code ?: strtoupper(Str::limit(Str::replace('-', '', $slug), 40, ''));
            $adminData = $data->admin;
            $databaseOverride = $data->database;
            $driverOverride = $data->driver;
            $dbHost = $data->dbHost;
            $dbPort = $data->dbPort;
            $dbUsername = $data->dbUsername;
            $dbPassword = $data->dbPassword;
            $domainOverride = $data->domain;
            $packageId = $data->packageId;
            $trialDays = $data->trialDays;
            $timezone = $data->timezone;
            $locale = $data->locale;
            $country = $data->country;
            $legalName = $data->legalName;
            $metadata = $data->metadata;
        } else {
            $name = trim((string) ($data['name'] ?? $data['company_name'] ?? ''));
            $slug = Str::slug((string) ($data['slug'] ?? $data['domain_prefix'] ?? $name));
            $rawCode = (string) ($data['code'] ?? strtoupper(Str::limit(Str::replace('-', '', $slug), 40, '')));
            $adminData = isset($data['admin']) && $data['admin'] instanceof TenantAdminData
                ? $data['admin']
                : (isset($data['admin_email']) || isset($data['admin_name']) || isset($data['admin']) ? TenantAdminData::fromArray((array) ($data['admin'] ?? $data)) : null);
            $databaseOverride = $data['database'] ?? null;
            $driverOverride = $data['driver'] ?? null;
            $dbHost = $data['db_host'] ?? null;
            $dbPort = isset($data['db_port']) ? (int) $data['db_port'] : null;
            $dbUsername = $data['db_username'] ?? null;
            $dbPassword = $data['db_password'] ?? null;
            $domainOverride = $data['domain'] ?? null;
            $packageId = $data['package_id'] ?? null;
            $trialDays = (int) ($data['trial_days'] ?? 14);
            $timezone = $data['timezone'] ?? 'Asia/Jakarta';
            $locale = $data['locale'] ?? 'id';
            $country = $data['country'] ?? 'ID';
            $legalName = $data['legal_name'] ?? null;
            $metadata = (array) ($data['metadata'] ?? []);
        }

        if ($name === '' || $slug === '') {
            throw ValidationException::withMessages(['name' => 'Tenant name and a valid slug are required.']);
        }

        $code = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '', $rawCode));
        if ($code === '') {
            $code = strtoupper(Str::limit(Str::replace('-', '', $slug), 40, ''));
        }

        $normalizedCode = Str::lower(preg_replace('/[^a-zA-Z0-9_]/', '_', $code));
        $databaseName = $databaseOverride ?: ('siakad_t_'.$normalizedCode);
        $driver = $driverOverride ?: (str_ends_with((string) $databaseName, '.sqlite') ? 'sqlite' : 'mysql');

        /** @var Tenant $tenant */
        $tenant = DB::connection('landlord')->transaction(function () use (
            $code,
            $name,
            $legalName,
            $slug,
            $timezone,
            $locale,
            $country,
            $metadata,
            $databaseName,
            $driver,
            $dbHost,
            $dbPort,
            $dbUsername,
            $dbPassword,
            $domainOverride,
            $packageId,
            $trialDays
        ): Tenant {
            $tenant = Tenant::query()->create([
                'code' => $code,
                'name' => $name,
                'legal_name' => $legalName,
                'slug' => $slug,
                'status' => TenantStatus::PENDING,
                'timezone' => $timezone,
                'locale' => $locale,
                'country' => $country,
                'metadata' => $metadata,
            ]);

            $tenant->database()->create([
                'name' => 'primary',
                'driver' => $driver,
                'host' => $dbHost ?? config('database.connections.tenant.host', '127.0.0.1'),
                'port' => $dbPort ?? (int) config('database.connections.tenant.port', 3306),
                'database' => $databaseName,
                'username' => $dbUsername ?? config('database.connections.tenant.username', 'root'),
                'password' => $dbPassword ?? config('database.connections.tenant.password', ''),
                'status' => TenantDatabaseStatus::PROVISIONING,
                'is_primary' => true,
            ]);

            $domain = strtolower((string) ($domainOverride ?: ($slug.'.'.config('tenancy.central_domains.0', 'localhost'))));
            $tenant->domains()->create([
                'domain' => $domain,
                'type' => DomainType::SUBDOMAIN,
                'is_primary' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            if (! empty($packageId)) {
                $tenant->subscriptions()->create([
                    'package_id' => $packageId,
                    'status' => 'trialing',
                    'starts_at' => now(),
                    'trial_ends_at' => now()->addDays($trialDays),
                    'auto_renew' => true,
                    'price' => 0,
                    'currency' => 'IDR',
                ]);
            }

            return $tenant;
        });

        // Trigger Event after transaction commits
        event(new TenantCreated((string) $tenant->getKey(), $adminData));

        return $tenant;
    }
}
