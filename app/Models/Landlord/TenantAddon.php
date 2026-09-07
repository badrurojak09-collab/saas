<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\AddonStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantAddon extends LandlordModel
{
    protected $table = 'tenant_addons';

    protected $fillable = [
        'tenant_id',
        'addon_id',
        'status',
        'starts_at',
        'ends_at',
        'price',
        'currency',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => AddonStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'price' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class, 'addon_id');
    }
}
