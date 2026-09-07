<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateCurriculumData;
use App\Events\Tenant\CurriculumPublished;
use App\Models\Tenant\Curriculum;

class CreateCurriculumAction
{
    public function execute(CreateCurriculumData $data): Curriculum
    {
        $curriculum = Curriculum::create([
            'study_program_id' => $data->studyProgramId,
            'code' => $data->code,
            'name' => $data->name,
            'description' => $data->description,
            'effective_start_year' => $data->effectiveStartYear,
            'effective_end_year' => $data->effectiveEndYear,
            'status' => $data->status,
        ]);

        if ($curriculum->status === 'active' || $curriculum->status === 'published') {
            event(new CurriculumPublished($curriculum));
        }

        return $curriculum;
    }
}
