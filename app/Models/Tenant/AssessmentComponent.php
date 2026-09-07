<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\AssessmentType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentComponent extends TenantModel
{
    protected $table = 'assessment_components';

    protected $fillable = [
        'class_group_id',
        'code',
        'name',
        'weight',
        'max_score',
        'assessment_type',
        'sequence',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'max_score' => 'decimal:2',
            'assessment_type' => AssessmentType::class,
            'sequence' => 'integer',
        ];
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'assessment_component_id');
    }
}
