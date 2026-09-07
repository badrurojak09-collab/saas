<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\TranscriptStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transcript extends TenantModel
{
    protected $table = 'transcripts';

    protected $fillable = [
        'student_id',
        'transcript_number',
        'generated_at',
        'total_credits',
        'final_gpa',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'total_credits' => 'decimal:1',
            'final_gpa' => 'decimal:2',
            'status' => TranscriptStatus::class,
            'generated_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TranscriptItem::class, 'transcript_id');
    }
}
