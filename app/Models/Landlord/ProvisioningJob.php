<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\ProvisioningJobStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProvisioningJob extends LandlordModel
{
    protected $table = 'provisioning_jobs';

    protected $fillable = [
        'tenant_id',
        'job_type',
        'status',
        'attempts',
        'started_at',
        'completed_at',
        'error_message',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProvisioningJobStatus::class,
            'attempts' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
