<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateAcademicYearData;
use App\Events\Tenant\AcademicYearCreated;
use App\Models\Tenant\AcademicYear;

class CreateAcademicYearAction
{
    public function execute(CreateAcademicYearData $data): AcademicYear
    {
        $year = AcademicYear::create([
            'code' => $data->code,
            'name' => $data->name,
            'start_date' => $data->startDate,
            'end_date' => $data->endDate,
            'status' => $data->status,
        ]);

        event(new AcademicYearCreated($year));

        return $year;
    }
}
