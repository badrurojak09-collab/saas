<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\TenantDatabaseStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantDatabase extends LandlordModel
{
    protected $table = 'tenant_databases';

    protected $fillable = [
        'tenant_id',
        'name',
        'driver',
        'host',
        'port',
        'database',
        'username',
        'password',
        'status',
        'schema_version',
        'is_primary',
        'last_migrated_at',
        'last_backup_at',
        'metadata',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'is_primary' => 'boolean',
            'status' => TenantDatabaseStatus::class,
            'last_migrated_at' => 'datetime',
            'last_backup_at' => 'datetime',
            'metadata' => 'array',
            'password' => 'encrypted',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(TenantDatabaseOperation::class, 'tenant_database_id');
    }

    public function migrationVersions(): HasMany
    {
        return $this->hasMany(TenantMigrationVersion::class, 'tenant_database_id');
    }
}
