<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends TenantModel
{
    use SoftDeletes;

    protected $table = 'buildings';

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'building_id');
    }
}
