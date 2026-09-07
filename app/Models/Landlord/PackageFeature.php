<?php

// app/Models/Landlord/PackageFeature.php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeature extends LandlordModel
{
    protected $fillable = [
        'package_id',
        'feature_code',
        'feature_name',
        'limits',
    ];

    protected function casts(): array
    {
        return [
            'limits' => 'array',  // Menyimpan JSON limit fitur
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
