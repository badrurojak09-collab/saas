<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\CreateDepartmentAction;
use App\Actions\Tenant\CreateFacultyAction;
use App\Actions\Tenant\CreateStudyProgramAction;
use App\Actions\Tenant\UpdateDepartmentAction;
use App\Actions\Tenant\UpdateFacultyAction;
use App\Actions\Tenant\UpdateStudyProgramAction;
use App\DTOs\Tenant\CreateDepartmentData;
use App\DTOs\Tenant\CreateFacultyData;
use App\DTOs\Tenant\CreateStudyProgramData;
use App\Models\Tenant\Department;
use App\Models\Tenant\Faculty;
use App\Models\Tenant\StudyProgram;
use Illuminate\Database\Eloquent\Collection;

class OrganizationService
{
    public function __construct(
        protected CreateFacultyAction $createFacultyAction,
        protected UpdateFacultyAction $updateFacultyAction,
        protected CreateDepartmentAction $createDepartmentAction,
        protected UpdateDepartmentAction $updateDepartmentAction,
        protected CreateStudyProgramAction $createStudyProgramAction,
        protected UpdateStudyProgramAction $updateStudyProgramAction,
    ) {}

    public function allFaculties(): Collection
    {
        return Faculty::with('departments.studyPrograms')->get();
    }

    public function createFaculty(CreateFacultyData $data): Faculty
    {
        return $this->createFacultyAction->execute($data);
    }

    public function updateFaculty(Faculty $faculty, CreateFacultyData $data): Faculty
    {
        return $this->updateFacultyAction->execute($faculty, $data);
    }

    public function createDepartment(CreateDepartmentData $data): Department
    {
        return $this->createDepartmentAction->execute($data);
    }

    public function updateDepartment(Department $department, CreateDepartmentData $data): Department
    {
        return $this->updateDepartmentAction->execute($department, $data);
    }

    public function createStudyProgram(CreateStudyProgramData $data): StudyProgram
    {
        return $this->createStudyProgramAction->execute($data);
    }

    public function updateStudyProgram(StudyProgram $studyProgram, CreateStudyProgramData $data): StudyProgram
    {
        return $this->updateStudyProgramAction->execute($studyProgram, $data);
    }
}
