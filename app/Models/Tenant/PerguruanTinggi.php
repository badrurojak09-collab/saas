<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\SoftDeletes;

class PerguruanTinggi extends TenantModel
{
    use SoftDeletes;

    protected $table = 'perguruan_tinggi';

    protected $fillable = [
        'code',
        'name',
        'short_name',
        'legal_name',
        'status',
        'address',
        'phone',
        'email',
        'website',
    ];
}
