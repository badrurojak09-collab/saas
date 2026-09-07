<?php

namespace Tests\Unit\Tenant\Actions;

use App\Actions\Tenant\AddCoursePrerequisiteAction;
use App\DTOs\Tenant\CreateAcademicYearData;
use App\DTOs\Tenant\CreateFacultyData;
use App\DTOs\Tenant\CreateSemesterData;
use App\DTOs\Tenant\CreateStudyProgramData;
use App\DTOs\Tenant\CreateDepartmentData;
use App\Actions\Tenant\ActivateSemesterAction;
use App\Actions\Tenant\CloseSemesterAction;
use App\Enums\Tenant\DegreeLevel;
use App\Enums\Tenant\SemesterType;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Unit tests for Tenant Action classes — tests only the object structure, 
 * DTO wiring, and boundary invariants (without a live database).
 */
final class TenantActionUnitTest extends TestCase
{
    // ─── DTO fromArray ─────────────────────────────────────────────────────────

    public function test_create_faculty_data_from_array(): void
    {
        $data = CreateFacultyData::fromArray([
            'code' => 'FTI',
            'name' => 'Fakultas Teknologi Informasi',
            'short_name' => 'FTI',
            'status' => 'active',
        ]);

        $this->assertSame('FTI', $data->code);
        $this->assertSame('Fakultas Teknologi Informasi', $data->name);
        $this->assertSame('FTI', $data->shortName);
        $this->assertSame('active', $data->status);
    }

    public function test_create_department_data_from_array(): void
    {
        $data = CreateDepartmentData::fromArray([
            'faculty_id' => 'faculty-123',
            'code' => 'IF',
            'name' => 'Informatika',
        ]);

        $this->assertSame('faculty-123', $data->facultyId);
        $this->assertSame('IF', $data->code);
        $this->assertSame('active', $data->status); // default
    }

    public function test_create_study_program_data_defaults_to_s1(): void
    {
        $data = CreateStudyProgramData::fromArray([
            'department_id' => 'dept-001',
            'code' => '55201',
            'name' => 'Teknik Informatika',
        ]);

        $this->assertSame(DegreeLevel::S1, $data->degreeLevel);
    }

    public function test_create_study_program_data_accepts_degree_level_string(): void
    {
        $data = CreateStudyProgramData::fromArray([
            'department_id' => 'dept-001',
            'code' => '55201',
            'name' => 'Teknik Informatika',
            'degree_level' => 's2',
        ]);

        $this->assertSame(DegreeLevel::S2, $data->degreeLevel);
    }

    public function test_create_academic_year_data_from_array(): void
    {
        $data = CreateAcademicYearData::fromArray([
            'code' => '2026/2027',
            'name' => 'Tahun Akademik 2026/2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-08-31',
        ]);

        $this->assertSame('2026/2027', $data->code);
        $this->assertSame('2026-09-01', $data->startDate);
    }

    public function test_create_semester_data_defaults_to_odd(): void
    {
        $data = CreateSemesterData::fromArray([
            'academic_year_id' => 'ay-001',
            'code' => '20261',
            'name' => 'Semester Ganjil',
        ]);

        $this->assertSame(SemesterType::Odd, $data->semesterType);
        $this->assertSame(1, $data->sequence);
        $this->assertFalse($data->isActive);
    }

    public function test_create_semester_data_accepts_semester_type_string(): void
    {
        $data = CreateSemesterData::fromArray([
            'academic_year_id' => 'ay-001',
            'code' => '20262',
            'name' => 'Semester Genap',
            'semester_type' => 'even',
        ]);

        $this->assertSame(SemesterType::Even, $data->semesterType);
    }

    // ─── Boundary Invariant ────────────────────────────────────────────────────

    public function test_add_course_prerequisite_action_rejects_self_reference(): void
    {
        $action = new AddCoursePrerequisiteAction();
        $sameId = 'course-aaaaaa';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Course cannot be its own prerequisite');

        $action->execute($sameId, $sameId);
    }
}
