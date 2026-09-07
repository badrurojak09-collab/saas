<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends LandlordModel
{
    protected $table = 'payment_transactions';

    protected $fillable = [
        'payment_id',
        'provider',
        'provider_transaction_id',
        'status',
        'request_payload',
        'response_payload',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
