<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\AddCoursePrerequisiteAction;
use App\Actions\Tenant\CreateCourseAction;
use App\Actions\Tenant\UpdateCourseAction;
use App\DTOs\Tenant\CreateCourseData;
use App\Models\Tenant\Course;
use App\Models\Tenant\CoursePrerequisite;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseService
{
    public function __construct(
        protected CreateCourseAction $createCourseAction,
        protected UpdateCourseAction $updateCourseAction,
        protected AddCoursePrerequisiteAction $addCoursePrerequisiteAction,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Course::query()->latest()->paginate($perPage);
    }

    public function create(CreateCourseData $data): Course
    {
        return $this->createCourseAction->execute($data);
    }

    public function update(Course $course, CreateCourseData $data): Course
    {
        return $this->updateCourseAction->execute($course, $data);
    }

    public function addPrerequisite(string $courseId, string $prerequisiteCourseId, string $minimumGrade = 'C'): CoursePrerequisite
    {
        return $this->addCoursePrerequisiteAction->execute($courseId, $prerequisiteCourseId, $minimumGrade);
    }

    public function delete(Course $course): bool
    {
        return (bool) $course->delete();
    }
}
