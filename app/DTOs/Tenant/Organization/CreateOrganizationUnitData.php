<?php

namespace App\DTOs\Tenant\Organization;

use App\Enums\Tenant\OrganizationUnitType;

readonly class CreateOrganizationUnitData
{
    public function __construct(
        public string $perguruanTinggiId,
        public ?string $parentId,
        public string $code,
        public string $name,
        public ?string $shortName,
        public OrganizationUnitType $type,
        public ?string $description,
        public int $sortOrder = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            perguruanTinggiId: $data['perguruan_tinggi_id'],
            parentId: $data['parent_id'] ?? null,
            code: $data['code'],
            name: $data['name'],
            shortName: $data['short_name'] ?? null,
            type: $data['type'] instanceof OrganizationUnitType ? $data['type'] : OrganizationUnitType::from($data['type']),
            description: $data['description'] ?? null,
            sortOrder: $data['sort_order'] ?? 0,
        );
    }
}
