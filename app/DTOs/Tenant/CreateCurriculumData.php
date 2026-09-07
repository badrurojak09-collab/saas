<?php

namespace App\DTOs\Tenant;

readonly class CreateCurriculumData
{
    public function __construct(
        public string $studyProgramId,
        public string $code,
        public string $name,
        public ?string $description = null,
        public int $effectiveStartYear = 2026,
        public ?int $effectiveEndYear = null,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            studyProgramId: $data['study_program_id'],
            code: $data['code'],
            name: $data['name'],
            description: $data['description'] ?? null,
            effectiveStartYear: (int) ($data['effective_start_year'] ?? date('Y')),
            effectiveEndYear: isset($data['effective_end_year']) ? (int) $data['effective_end_year'] : null,
            status: $data['status'] ?? 'active',
        );
    }
}
