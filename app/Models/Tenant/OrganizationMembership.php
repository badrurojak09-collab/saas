<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\OrganizationMembershipType;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class OrganizationMembership extends TenantModel
{
    use SoftDeletes;

    protected $table = 'organization_memberships';

    protected $fillable = [
        'user_id',
        'organization_unit_id',
        'membership_type',
        'is_primary',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'membership_type' => OrganizationMembershipType::class,
            'is_primary' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function isActive(): bool
    {
        return (! $this->starts_at || $this->starts_at instanceof CarbonInterface && $this->starts_at->isPast())
            && (! $this->ends_at || $this->ends_at instanceof CarbonInterface && $this->ends_at->isFuture())
            && ! $this->trashed();
    }
}
