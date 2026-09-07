<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\UserStatus;

readonly class UpdateUserData
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?UserStatus $status = null,
        public ?array $roles = null,
        public ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            username: $data['username'] ?? null,
            password: $data['password'] ?? null,
            status: isset($data['status'])
                ? ($data['status'] instanceof UserStatus ? $data['status'] : UserStatus::from($data['status']))
                : null,
            roles: $data['roles'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }
}
