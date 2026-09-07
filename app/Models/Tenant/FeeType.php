<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\FeeBillingFrequency;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends TenantModel
{
    use SoftDeletes;

    protected $table = 'fee_types';

    protected $fillable = [
        'code',
        'name',
        'description',
        'default_amount',
        'billing_frequency',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'default_amount' => 'decimal:2',
            'billing_frequency' => FeeBillingFrequency::class,
        ];
    }

    public function bills(): HasMany
    {
        return $this->hasMany(StudentBill::class, 'fee_type_id');
    }
}
