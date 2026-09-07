<?php

namespace App\Actions\Tenant;

use App\Events\Tenant\SemesterClosed;
use App\Models\Tenant\Semester;

class CloseSemesterAction
{
    public function execute(Semester $semester): Semester
    {
        $semester->update(['is_active' => false]);

        event(new SemesterClosed($semester));

        return $semester->fresh();
    }
}
