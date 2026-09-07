<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantMigrationVersion extends LandlordModel
{
    public $timestamps = false;

    protected $table = 'tenant_migration_versions';

    protected $fillable = [
        'tenant_database_id',
        'migration',
        'batch',
        'applied_at',
        'execution_time_ms',
    ];

    protected function casts(): array
    {
        return [
            'batch' => 'integer',
            'applied_at' => 'datetime',
            'execution_time_ms' => 'integer',
        ];
    }

    public function tenantDatabase(): BelongsTo
    {
        return $this->belongsTo(TenantDatabase::class, 'tenant_database_id');
    }
}
