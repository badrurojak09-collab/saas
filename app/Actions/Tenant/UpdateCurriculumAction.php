<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateCurriculumData;
use App\Events\Tenant\CurriculumPublished;
use App\Models\Tenant\Curriculum;

class UpdateCurriculumAction
{
    public function execute(Curriculum $curriculum, CreateCurriculumData $data): Curriculum
    {
        $oldStatus = $curriculum->status;

        $curriculum->update([
            'study_program_id' => $data->studyProgramId,
            'code' => $data->code,
            'name' => $data->name,
            'description' => $data->description,
            'effective_start_year' => $data->effectiveStartYear,
            'effective_end_year' => $data->effectiveEndYear,
            'status' => $data->status,
        ]);

        if ($oldStatus !== 'active' && $data->status === 'active') {
            event(new CurriculumPublished($curriculum));
        }

        return $curriculum->fresh();
    }
}
