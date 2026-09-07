<?php

namespace App\Models\Landlord;

use App\Contracts\Tenancy\IdentifiesTenant;
use App\Enums\Landlord\TenantStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends LandlordModel implements IdentifiesTenant
{
    use SoftDeletes;

    protected $table = 'tenants';

    protected $fillable = [
        'code',
        'name',
        'legal_name',
        'slug',
        'status',
        'timezone',
        'locale',
        'country',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => TenantStatus::class,
            'metadata' => 'array',
        ];
    }

    public function getTenantKey(): string
    {
        return (string) $this->getKey();
    }

    public function getDatabaseName(): ?string
    {
        return TenantDatabase::query()
            ->where('tenant_id', $this->getKey())
            ->where('is_primary', true)
            ->value('database');
    }

    // --- Relasi Landlord DB ---

    // 1 Tenant = 1 DB Metadata
    public function database(): HasOne
    {
        return $this->hasOne(TenantDatabase::class, 'tenant_id');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'tenant_id');
    }

    public function primaryDomain(): HasOne
    {
        return $this->hasOne(Domain::class, 'tenant_id')->where('is_primary', true);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class, 'tenant_id');
    }

    public function latestSubscription(): HasOne
    {
        return $this->hasOne(TenantSubscription::class, 'tenant_id')->latestOfMany();
    }

    public function addons(): HasMany
    {
        return $this->hasMany(TenantAddon::class, 'tenant_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'tenant_id');
    }

    public function usage(): HasMany
    {
        return $this->hasMany(TenantUsage::class, 'tenant_id');
    }

    public function provisioningJobs(): HasMany
    {
        return $this->hasMany(ProvisioningJob::class, 'tenant_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'tenant_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PlatformAuditLog::class, 'tenant_id');
    }
}
