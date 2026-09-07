<?php

namespace App\Enums\Tenant;

enum CourseCategory: string
{
    case General = 'general';
    case BasicScience = 'basic_science';
    case Core = 'core';
    case Specialization = 'specialization';
    case Internship = 'internship';
    case Thesis = 'thesis';
}
