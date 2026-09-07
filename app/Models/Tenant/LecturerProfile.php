<?php

namespace App\Models\Tenant;

final class LecturerProfile extends Profile
{
    protected $table = 'lecturer_profiles';

    protected $fillable = ['user_id', 'employee_number', 'full_name', 'status', 'metadata'];
}
