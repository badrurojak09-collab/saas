<?php

namespace App\DTOs\Tenant;

readonly class CreateDepartmentData
{
    public function __construct(
        public string $facultyId,
        public string $code,
        public string $name,
        public ?string $shortName = null,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            facultyId: $data['faculty_id'],
            code: $data['code'],
            name: $data['name'],
            shortName: $data['short_name'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }
}
