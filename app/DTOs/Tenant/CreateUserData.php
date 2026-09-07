<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\UserStatus;

readonly class CreateUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $username = null,
        public ?string $password = null,
        public UserStatus $status = UserStatus::Active,
        public array $roles = [],
        public array $metadata = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            username: $data['username'] ?? null,
            password: $data['password'] ?? null,
            status: isset($data['status'])
                ? ($data['status'] instanceof UserStatus ? $data['status'] : UserStatus::from($data['status']))
                : UserStatus::Active,
            roles: $data['roles'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }
}
