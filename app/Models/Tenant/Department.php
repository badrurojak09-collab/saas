<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends TenantModel
{
    use SoftDeletes;

    protected $table = 'departments';

    protected $fillable = [
        'faculty_id',
        'code',
        'name',
        'short_name',
        'status',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class, 'department_id');
    }
}
