<?php

namespace App\DTOs\Tenant;

readonly class CreateAcademicYearData
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }
}
