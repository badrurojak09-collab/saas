<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\StudentStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends TenantModel
{
    use SoftDeletes;

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'student_number',
        'national_student_number',
        'study_program_id',
        'entry_year',
        'entry_semester_id',
        'admission_type',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'nik',
        'phone',
        'email',
        'address',
        'status',
        'graduation_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => StudentStatus::class,
            'birth_date' => 'date',
            'graduation_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    public function entrySemester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'entry_semester_id');
    }

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class, 'student_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    public function assessmentScores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'student_id');
    }

    public function finalGrades(): HasMany
    {
        return $this->hasMany(FinalGrade::class, 'student_id');
    }

    public function academicResults(): HasMany
    {
        return $this->hasMany(AcademicResult::class, 'student_id');
    }

    public function transcript(): HasOne
    {
        return $this->hasOne(Transcript::class, 'student_id');
    }

    public function graduationRecord(): HasOne
    {
        return $this->hasOne(GraduationRecord::class, 'student_id');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(StudentBill::class, 'student_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class, 'student_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'student_id');
    }
}
