<?php

namespace App\DTOs\Tenant;

readonly class CreateBuildingData
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $description = null,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }
}
