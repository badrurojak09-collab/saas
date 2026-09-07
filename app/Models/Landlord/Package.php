<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\BillingCycle;
use App\Enums\Landlord\PackageStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends LandlordModel
{
    use SoftDeletes;

    protected $table = 'packages';

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'billing_cycle',
        'status',
        'limits',
        'metadata',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'billing_cycle' => BillingCycle::class,
            'status' => PackageStatus::class,
            'limits' => 'array',
            'metadata' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function features(): HasMany
    {
        return $this->hasMany(PackageFeature::class, 'package_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class, 'package_id');
    }
}
