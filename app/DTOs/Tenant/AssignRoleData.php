<?php

namespace App\DTOs\Tenant;

readonly class AssignRoleData
{
    /**
     * @param  string|array<int, string>  $roles
     */
    public function __construct(
        public string $userId,
        public string|array $roles,
        public ?string $performedBy = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            roles: $data['roles'],
            performedBy: $data['performed_by'] ?? null,
        );
    }
}
