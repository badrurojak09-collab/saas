<?php

namespace App\Enums\Tenant;

enum UserType: string
{
    case Admin = 'admin';
    case Staff = 'staff';
    case Academic = 'academic';
    case Student = 'student';
    case Lecturer = 'lecturer';
}
