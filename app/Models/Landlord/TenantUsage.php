<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantUsage extends LandlordModel
{
    protected $table = 'tenant_usage';

    protected $fillable = [
        'tenant_id',
        'metric',
        'value',
        'measured_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'measured_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
