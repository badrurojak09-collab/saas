<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\ActivateSemesterAction;
use App\Actions\Tenant\CloseSemesterAction;
use App\Actions\Tenant\CreateAcademicYearAction;
use App\Actions\Tenant\CreateSemesterAction;
use App\DTOs\Tenant\CreateAcademicYearData;
use App\DTOs\Tenant\CreateSemesterData;
use App\Models\Tenant\AcademicYear;
use App\Models\Tenant\Semester;
use Illuminate\Database\Eloquent\Collection;

class AcademicPeriodService
{
    public function __construct(
        protected CreateAcademicYearAction $createAcademicYearAction,
        protected CreateSemesterAction $createSemesterAction,
        protected ActivateSemesterAction $activateSemesterAction,
        protected CloseSemesterAction $closeSemesterAction,
    ) {}

    public function allAcademicYears(): Collection
    {
        return AcademicYear::with('semesters')->latest()->get();
    }

    public function getActiveSemester(): ?Semester
    {
        return Semester::query()->where('is_active', true)->first();
    }

    public function createAcademicYear(CreateAcademicYearData $data): AcademicYear
    {
        return $this->createAcademicYearAction->execute($data);
    }

    public function createSemester(CreateSemesterData $data): Semester
    {
        return $this->createSemesterAction->execute($data);
    }

    public function activateSemester(Semester $semester): Semester
    {
        return $this->activateSemesterAction->execute($semester);
    }

    public function closeSemester(Semester $semester): Semester
    {
        return $this->closeSemesterAction->execute($semester);
    }
}
