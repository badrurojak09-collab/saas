<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\DegreeLevel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyProgram extends TenantModel
{
    use SoftDeletes;

    protected $table = 'study_programs';

    protected $fillable = [
        'department_id',
        'code',
        'name',
        'degree_level',
        'accreditation_status',
        'accreditation_number',
        'accreditation_expired_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'degree_level' => DegreeLevel::class,
            'accreditation_expired_at' => 'date',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'study_program_id');
    }

    public function curriculums(): HasMany
    {
        return $this->hasMany(Curriculum::class, 'study_program_id');
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'study_program_id');
    }
}
