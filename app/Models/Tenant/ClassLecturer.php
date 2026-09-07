<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassLecturer extends TenantModel
{
    protected $table = 'class_lecturers';

    protected $fillable = [
        'class_group_id',
        'lecturer_id',
        'role',
    ];

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
}
