<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\DatabaseOperationStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantDatabaseOperation extends LandlordModel
{
    protected $table = 'tenant_database_operations';

    protected $fillable = [
        'tenant_database_id',
        'operation',
        'status',
        'started_at',
        'completed_at',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => DatabaseOperationStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function tenantDatabase(): BelongsTo
    {
        return $this->belongsTo(TenantDatabase::class, 'tenant_database_id');
    }
}
