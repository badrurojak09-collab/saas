<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\AddCurriculumCourseAction;
use App\Actions\Tenant\CreateCurriculumAction;
use App\Actions\Tenant\UpdateCurriculumAction;
use App\DTOs\Tenant\AddCurriculumCourseData;
use App\DTOs\Tenant\CreateCurriculumData;
use App\Models\Tenant\Curriculum;
use App\Models\Tenant\CurriculumCourse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CurriculumService
{
    public function __construct(
        protected CreateCurriculumAction $createCurriculumAction,
        protected UpdateCurriculumAction $updateCurriculumAction,
        protected AddCurriculumCourseAction $addCurriculumCourseAction,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Curriculum::with('studyProgram')->latest()->paginate($perPage);
    }

    public function create(CreateCurriculumData $data): Curriculum
    {
        return $this->createCurriculumAction->execute($data);
    }

    public function update(Curriculum $curriculum, CreateCurriculumData $data): Curriculum
    {
        return $this->updateCurriculumAction->execute($curriculum, $data);
    }

    public function addCourse(AddCurriculumCourseData $data): CurriculumCourse
    {
        return $this->addCurriculumCourseAction->execute($data);
    }

    public function delete(Curriculum $curriculum): bool
    {
        return (bool) $curriculum->delete();
    }
}
