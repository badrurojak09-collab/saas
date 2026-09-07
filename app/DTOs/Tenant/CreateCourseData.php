<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\CourseCategory;
use App\Enums\Tenant\CourseType;
use App\Enums\Tenant\GradingType;

readonly class CreateCourseData
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $shortName = null,
        public ?string $description = null,
        public float $creditUnits = 3.0,
        public CourseType $courseType = CourseType::Mandatory,
        public CourseCategory $courseCategory = CourseCategory::General,
        public GradingType $gradingType = GradingType::StandardLetter,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            shortName: $data['short_name'] ?? null,
            description: $data['description'] ?? null,
            creditUnits: (float) ($data['credit_units'] ?? 3.0),
            courseType: isset($data['course_type'])
                ? ($data['course_type'] instanceof CourseType ? $data['course_type'] : CourseType::from($data['course_type']))
                : CourseType::Mandatory,
            courseCategory: isset($data['course_category'])
                ? ($data['course_category'] instanceof CourseCategory ? $data['course_category'] : CourseCategory::from($data['course_category']))
                : CourseCategory::Major,
            gradingType: isset($data['grading_type'])
                ? ($data['grading_type'] instanceof GradingType ? $data['grading_type'] : GradingType::from($data['grading_type']))
                : GradingType::Standard,
            status: $data['status'] ?? 'active',
        );
    }
}
