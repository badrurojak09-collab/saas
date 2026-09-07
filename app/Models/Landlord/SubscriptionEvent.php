<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionEvent extends LandlordModel
{
    protected $table = 'subscription_events';

    protected $fillable = [
        'tenant_subscription_id',
        'event_type',
        'old_status',
        'new_status',
        'payload',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(TenantSubscription::class, 'tenant_subscription_id');
    }
}
