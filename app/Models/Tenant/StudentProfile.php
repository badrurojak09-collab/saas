<?php

namespace App\Models\Tenant;

final class StudentProfile extends Profile
{
    protected $table = 'student_profiles';

    protected $fillable = ['user_id', 'student_number', 'full_name', 'status', 'metadata'];
}
