<?php

namespace App\DTOs\Tenant;

readonly class AddCurriculumCourseData
{
    public function __construct(
        public string $curriculumId,
        public string $courseId,
        public int $semesterNumber = 1,
        public ?string $courseGroup = null,
        public bool $isMandatory = true,
        public float $creditUnits = 3.0,
        public ?string $minimumGrade = 'C',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            curriculumId: $data['curriculum_id'],
            courseId: $data['course_id'],
            semesterNumber: (int) ($data['semester_number'] ?? 1),
            courseGroup: $data['course_group'] ?? null,
            isMandatory: (bool) ($data['is_mandatory'] ?? true),
            creditUnits: (float) ($data['credit_units'] ?? 3.0),
            minimumGrade: $data['minimum_grade'] ?? 'C',
        );
    }
}
