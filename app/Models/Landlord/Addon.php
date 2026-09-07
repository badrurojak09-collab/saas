<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\AddonStatus;
use App\Enums\Landlord\BillingCycle;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addon extends LandlordModel
{
    use HasUuidV7;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'billing_cycle',
        'status',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'billing_cycle' => BillingCycle::class,
            'status' => AddonStatus::class,
            'config' => 'array',
        ];
    }

    public function features(): HasMany
    {
        return $this->hasMany(AddonFeature::class);
    }

    /**
     * Model TenantAddon belum dibuat (menyusul di step Subscription/TenantAddon).
     */
    public function tenantAddons(): HasMany
    {
        return $this->hasMany(TenantAddon::class);
    }
}
