<?php

namespace App\DTOs\Tenant;

readonly class CreateFacultyData
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $shortName = null,
        public ?string $description = null,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            shortName: $data['short_name'] ?? null,
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }
}
