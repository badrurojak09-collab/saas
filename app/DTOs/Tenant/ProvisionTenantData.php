<?php

namespace App\DTOs\Tenant;

readonly class ProvisionTenantData
{
    public function __construct(
        public string $name,
        public ?string $code = null,
        public ?string $slug = null,
        public ?string $legalName = null,
        public string $timezone = 'Asia/Jakarta',
        public string $locale = 'id',
        public string $country = 'ID',
        public ?string $domain = null,
        public ?string $database = null,
        public ?string $driver = null,
        public ?string $dbHost = null,
        public ?int $dbPort = null,
        public ?string $dbUsername = null,
        public ?string $dbPassword = null,
        public ?string $packageId = null,
        public int $trialDays = 14,
        public array $metadata = [],
        public ?TenantAdminData $admin = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $admin = null;
        if (isset($data['admin']) && is_array($data['admin'])) {
            $admin = TenantAdminData::fromArray($data['admin']);
        } elseif (isset($data['admin_email']) || isset($data['admin_name'])) {
            $admin = TenantAdminData::fromArray($data);
        }

        return new self(
            name: (string) ($data['name'] ?? $data['company_name'] ?? ''),
            code: isset($data['code']) ? (string) $data['code'] : null,
            slug: isset($data['slug']) ? (string) $data['slug'] : (isset($data['domain_prefix']) ? (string) $data['domain_prefix'] : null),
            legalName: isset($data['legal_name']) ? (string) $data['legal_name'] : null,
            timezone: (string) ($data['timezone'] ?? 'Asia/Jakarta'),
            locale: (string) ($data['locale'] ?? 'id'),
            country: (string) ($data['country'] ?? 'ID'),
            domain: isset($data['domain']) ? (string) $data['domain'] : null,
            database: isset($data['database']) ? (string) $data['database'] : null,
            driver: isset($data['driver']) ? (string) $data['driver'] : null,
            dbHost: isset($data['db_host']) ? (string) $data['db_host'] : null,
            dbPort: isset($data['db_port']) ? (int) $data['db_port'] : null,
            dbUsername: isset($data['db_username']) ? (string) $data['db_username'] : null,
            dbPassword: isset($data['db_password']) ? (string) $data['db_password'] : null,
            packageId: isset($data['package_id']) ? (string) $data['package_id'] : null,
            trialDays: (int) ($data['trial_days'] ?? 14),
            metadata: (array) ($data['metadata'] ?? []),
            admin: $admin,
        );
    }
}
