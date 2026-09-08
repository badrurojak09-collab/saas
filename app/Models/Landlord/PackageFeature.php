<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeature extends LandlordModel
{
    protected $table = 'package_features';

    protected $fillable = [
        'package_id',
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

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
