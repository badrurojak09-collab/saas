<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\OrganizationUnitStatus;
use App\Enums\Tenant\OrganizationUnitType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationUnit extends TenantModel
{
    use SoftDeletes;

    protected $table = 'organization_units';

    protected $fillable = [
        'parent_id',
        'perguruan_tinggi_id',
        'code',
        'name',
        'short_name',
        'type',
        'status',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => OrganizationUnitType::class,
            'status' => OrganizationUnitStatus::class,
            'sort_order' => 'integer',
        ];
    }

    public function perguruanTinggi(): BelongsTo
    {
        return $this->belongsTo(PerguruanTinggi::class, 'perguruan_tinggi_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class, 'organization_unit_id');
    }

    public function activate(): void
    {
        $this->update(['status' => OrganizationUnitStatus::Active]);
        $this->fireModelEvent('activated', false);
    }

    public function deactivate(): void
    {
        $this->update(['status' => OrganizationUnitStatus::Inactive]);
        $this->fireModelEvent('deactivated', false);
    }
}
