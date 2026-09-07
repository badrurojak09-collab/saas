<?php

namespace App\Events\Tenant;

use App\Models\Tenant\Semester;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SemesterClosed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Semester $semester
    ) {}
}
