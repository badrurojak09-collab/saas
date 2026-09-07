<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\ChangeStudentStatusAction;
use App\Actions\Tenant\CreateStudentAction;
use App\Actions\Tenant\UpdateStudentAction;
use App\DTOs\Tenant\CreateStudentData;
use App\Enums\Tenant\StudentStatus;
use App\Models\Tenant\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class StudentService
{
    public function __construct(
        protected CreateStudentAction $createStudentAction,
        protected UpdateStudentAction $updateStudentAction,
        protected ChangeStudentStatusAction $changeStudentStatusAction,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Student::with(['user', 'studyProgram', 'entrySemester'])->latest()->paginate($perPage);
    }

    public function create(CreateStudentData $data): Student
    {
        if (Student::query()->where('student_number', $data->studentNumber)->exists()) {
            throw new InvalidArgumentException("Student number {$data->studentNumber} is already registered.");
        }

        return $this->createStudentAction->execute($data);
    }

    public function update(Student $student, CreateStudentData $data): Student
    {
        return $this->updateStudentAction->execute($student, $data);
    }

    public function changeStatus(
        Student $student,
        StudentStatus $newStatus,
        ?string $reason = null,
        ?string $userId = null
    ): Student {
        return $this->changeStudentStatusAction->execute($student, $newStatus, $reason, $userId);
    }

    public function delete(Student $student): bool
    {
        return (bool) $student->delete();
    }
}
