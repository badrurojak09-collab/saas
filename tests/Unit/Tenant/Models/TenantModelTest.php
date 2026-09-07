<?php

namespace Tests\Unit\Tenant\Models;

use App\Enums\Tenant\CourseCategory;
use App\Enums\Tenant\CourseType;
use App\Enums\Tenant\DegreeLevel;
use App\Enums\Tenant\EmploymentStatus;
use App\Enums\Tenant\GradingType;
use App\Enums\Tenant\SemesterType;
use App\Enums\Tenant\StudentStatus;
use App\Enums\Tenant\UserStatus;
use App\Models\Concerns\HasUuidV7;
use App\Models\Tenant\AcademicYear;
use App\Models\Tenant\Building;
use App\Models\Tenant\Course;
use App\Models\Tenant\Curriculum;
use App\Models\Tenant\Department;
use App\Models\Tenant\Faculty;
use App\Models\Tenant\Room;
use App\Models\Tenant\Semester;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudyProgram;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

final class TenantModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $tenant = new \App\Models\Landlord\Tenant([
            'code' => 'UNIT-TEST',
            'status' => \App\Enums\Landlord\TenantStatus::ACTIVE,
        ]);
        $tenant->id = '01900000-0000-7000-8000-000000000001';
        $tenant->code = 'UNIT-TEST';
        $tenant->status = \App\Enums\Landlord\TenantStatus::ACTIVE;

        $connManager = new class extends \App\Tenancy\Connection\TenantConnectionManager {
            public function connect(): void {}
        };
        $dbService = new class extends \App\Services\Tenant\TenantDatabaseService {
            public function loadMetadata(\App\Models\Landlord\Tenant $tenant): \App\Models\Landlord\TenantDatabase {
                $db = new \App\Models\Landlord\TenantDatabase(['database' => 'test_db']);
                $db->status = \App\Enums\Landlord\TenantDatabaseStatus::READY;
                return $db;
            }
        };

        $manager = new \App\Tenancy\Services\TenantManagerService(
            new \App\Tenancy\Context\TenantContext(),
            $connManager,
            $dbService
        );
        $manager->initialize($tenant);
        $this->app->instance(\App\Tenancy\Contracts\TenantManager::class, $manager);
    }

    protected function tearDown(): void
    {
        if ($this->app->bound(\App\Tenancy\Contracts\TenantManager::class)) {
            $this->app->make(\App\Tenancy\Contracts\TenantManager::class)->end();
        }
        parent::tearDown();
    }

    // ─── Connexion ────────────────────────────────────────────────────────────

    public function test_all_tenant_models_use_tenant_connection(): void
    {
        $models = [
            User::class,
            Faculty::class,
            Department::class,
            StudyProgram::class,
            AcademicYear::class,
            Semester::class,
            Course::class,
            Curriculum::class,
            Building::class,
            Room::class,
        ];

        foreach ($models as $model) {
            $this->assertSame(
                'tenant',
                (new $model)->getConnectionName(),
                "{$model} should use 'tenant' connection"
            );
        }
    }

    // ─── Traits ───────────────────────────────────────────────────────────────

    public function test_tenant_model_uses_has_uuid_v7_trait(): void
    {
        $traits = class_uses_recursive(Faculty::class);
        $this->assertContains(HasUuidV7::class, $traits);
    }

    public function test_tenant_models_use_has_factory(): void
    {
        $traits = class_uses_recursive(Faculty::class);
        $this->assertContains(HasFactory::class, $traits);
    }

    public function test_soft_deletable_models_have_soft_deletes(): void
    {
        $softDeleteModels = [
            Faculty::class,
            Department::class,
            StudyProgram::class,
        ];

        foreach ($softDeleteModels as $model) {
            $traits = class_uses_recursive($model);
            $this->assertContains(SoftDeletes::class, $traits, "{$model} should use SoftDeletes");
        }
    }

    // ─── Enum Casts ───────────────────────────────────────────────────────────

    public function test_user_status_is_cast_correctly(): void
    {
        $casts = (new User)->getCasts();
        $this->assertArrayHasKey('status', $casts);
        $this->assertSame(UserStatus::class, $casts['status']);
    }

    public function test_student_status_is_cast_correctly(): void
    {
        $casts = (new Student)->getCasts();
        $this->assertArrayHasKey('status', $casts);
        $this->assertSame(StudentStatus::class, $casts['status']);
    }

    // ─── Factory Resolver ─────────────────────────────────────────────────────

    public function test_tenant_models_resolve_to_correct_factory(): void
    {
        $factory = Faculty::factory();
        $this->assertInstanceOf(
            \Database\Factories\Tenant\FacultyFactory::class,
            $factory
        );
    }

    public function test_enum_values_are_defined(): void
    {
        $this->assertSame('odd', SemesterType::Odd->value);
        $this->assertSame('even', SemesterType::Even->value);
        $this->assertSame('active', StudentStatus::Active->value);
        $this->assertSame('graduated', StudentStatus::Graduated->value);
        $this->assertSame('permanent', EmploymentStatus::Permanent->value);
        $this->assertSame('mandatory', CourseType::Mandatory->value);
        $this->assertSame('classroom', \App\Enums\Tenant\RoomType::Classroom->value);
        $this->assertSame('s1', DegreeLevel::S1->value);
        $this->assertSame('standard_letter', GradingType::StandardLetter->value);
        $this->assertSame('general', CourseCategory::General->value);
    }

    // ─── Primary Key / UUID ───────────────────────────────────────────────────

    public function test_tenant_model_primary_key_is_uuid(): void
    {
        $model = new Faculty();
        $this->assertSame('id', $model->getKeyName());
        $this->assertFalse($model->getIncrementing());
        $this->assertSame('string', $model->getKeyType());
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function test_faculty_has_many_departments(): void
    {
        $faculty = new Faculty();
        $rel = $faculty->departments();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $rel);
    }

    public function test_department_belongs_to_faculty(): void
    {
        $dep = new Department();
        $rel = $dep->faculty();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $rel);
    }

    public function test_study_program_belongs_to_department(): void
    {
        $sp = new StudyProgram();
        $rel = $sp->department();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $rel);
    }

    public function test_academic_year_has_many_semesters(): void
    {
        $ay = new AcademicYear();
        $rel = $ay->semesters();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $rel);
    }

    public function test_semester_belongs_to_academic_year(): void
    {
        $sem = new Semester();
        $rel = $sem->academicYear();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $rel);
    }

    public function test_room_belongs_to_building(): void
    {
        $room = new Room();
        $rel = $room->building();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $rel);
    }

    public function test_curriculum_belongs_to_study_program(): void
    {
        $curr = new Curriculum();
        $rel = $curr->studyProgram();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $rel);
    }

    public function test_student_belongs_to_study_program(): void
    {
        $student = new Student();
        $rel = $student->studyProgram();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $rel);
    }

    public function test_student_belongs_to_user(): void
    {
        $student = new Student();
        $rel = $student->user();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $rel);
    }

    public function test_user_has_one_student(): void
    {
        $user = new User();
        $rel = $user->student();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $rel);
    }
}
