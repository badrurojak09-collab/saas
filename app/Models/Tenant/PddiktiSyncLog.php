<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\PddiktiOperation;
use App\Enums\Tenant\PddiktiSyncStatus;

class PddiktiSyncLog extends TenantModel
{
    public $timestamps = false;

    protected $table = 'pddikti_sync_logs';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'operation',
        'request_id',
        'status',
        'request_payload',
        'response_payload',
        'error_message',
        'started_at',
        'completed_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'operation' => PddiktiOperation::class,
            'status' => PddiktiSyncStatus::class,
            'request_payload' => 'array',
            'response_payload' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
