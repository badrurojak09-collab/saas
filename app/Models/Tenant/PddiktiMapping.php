<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\PddiktiSyncStatus;

class PddiktiMapping extends TenantModel
{
    protected $table = 'pddikti_mappings';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'external_id',
        'external_code',
        'sync_status',
        'last_synced_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'sync_status' => PddiktiSyncStatus::class,
            'last_synced_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
