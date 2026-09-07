<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\EmploymentStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecturer extends TenantModel
{
    use SoftDeletes;

    protected $table = 'lecturers';

    protected $fillable = [
        'user_id',
        'employee_number',
        'nidn',
        'name',
        'academic_title',
        'gender',
        'birth_place',
        'birth_date',
        'phone',
        'email',
        'employment_status',
        'joined_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'employment_status' => EmploymentStatus::class,
            'birth_date' => 'date',
            'joined_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classLecturers(): HasMany
    {
        return $this->hasMany(ClassLecturer::class, 'lecturer_id');
    }
}
