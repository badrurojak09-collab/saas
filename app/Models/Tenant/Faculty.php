<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends TenantModel
{
    use SoftDeletes;

    protected $table = 'faculties';

    protected $fillable = [
        'code',
        'name',
        'short_name',
        'description',
        'status',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'faculty_id');
    }
}
