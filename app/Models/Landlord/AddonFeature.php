<?php

namespace App\Models\Landlord;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddonFeature extends LandlordModel
{
    use HasUuidV7;

    protected $fillable = [
        'addon_id',
        'feature_code',
        'enabled',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'config' => 'array',
        ];
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class);
    }
}
