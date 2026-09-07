<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends TenantModel
{
    protected $table = 'document_types';

    protected $fillable = [
        'code',
        'name',
        'description',
        'entity_type',
        'is_required',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function studentDocuments(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'document_type_id');
    }
}
