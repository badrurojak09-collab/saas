<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\SemesterType;

readonly class CreateSemesterData
{
    public function __construct(
        public string $academicYearId,
        public string $code,
        public string $name,
        public int $sequence = 1,
        public SemesterType $semesterType = SemesterType::Odd,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public bool $isActive = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            academicYearId: $data['academic_year_id'],
            code: $data['code'],
            name: $data['name'],
            sequence: (int) ($data['sequence'] ?? 1),
            semesterType: isset($data['semester_type'])
                ? ($data['semester_type'] instanceof SemesterType ? $data['semester_type'] : SemesterType::from($data['semester_type']))
                : SemesterType::Odd,
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
            isActive: (bool) ($data['is_active'] ?? false),
        );
    }
}
