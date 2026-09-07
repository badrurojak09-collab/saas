<?php

namespace App\Models\Tenant;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrganizationMembership extends TenantModel
{
    protected $table = 'organization_memberships';

    protected $fillable = [
        'user_id',
        'organization_type',
        'organization_id',
        'membership_type',
        'is_primary',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isActive(): bool
    {
        return (! $this->starts_at || $this->starts_at instanceof CarbonInterface && $this->starts_at->isPast())
            && (! $this->ends_at || $this->ends_at instanceof CarbonInterface && $this->ends_at->isFuture());
    }
}
