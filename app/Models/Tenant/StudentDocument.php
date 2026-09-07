<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\DocumentStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDocument extends TenantModel
{
    use SoftDeletes;

    protected $table = 'student_documents';

    protected $fillable = [
        'student_id',
        'document_type_id',
        'file_name',
        'file_path',
        'disk',
        'mime_type',
        'file_size',
        'checksum',
        'uploaded_by',
        'verified_by',
        'verified_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'status' => DocumentStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
