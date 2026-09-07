<?php

namespace App\Actions\Tenant;

use App\Events\Tenant\SemesterActivated;
use App\Models\Tenant\Semester;
use Illuminate\Support\Facades\DB;

class ActivateSemesterAction
{
    public function execute(Semester $semester): Semester
    {
        return DB::connection('tenant')->transaction(function () use ($semester) {
            // Deactivate all other semesters
            Semester::query()->where('id', '!=', $semester->id)->update(['is_active' => false]);

            $semester->update(['is_active' => true]);

            event(new SemesterActivated($semester));

            return $semester->fresh();
        });
    }
}
