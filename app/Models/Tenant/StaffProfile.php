<?php

namespace App\Models\Tenant;

final class StaffProfile extends Profile
{
    protected $table = 'staff_profiles';

    protected $fillable = ['user_id', 'employee_number', 'full_name', 'status', 'metadata'];
}
