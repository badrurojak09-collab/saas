<?php

namespace App\Events\Tenant;

use App\Models\Tenant\AcademicYear;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicYearCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AcademicYear $academicYear
    ) {}
}
