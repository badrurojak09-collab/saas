<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\UserStatus;
use App\Enums\Tenant\UserType;

readonly class UpdateTenantUserData
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?string $phone = null,
        public ?string $avatarPath = null,
        public ?UserStatus $status = null,
        public ?UserType $userType = null,
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
            phone: $data['phone'] ?? null,
            avatarPath: $data['avatar_path'] ?? null,
            status: isset($data['status'])
                ? ($data['status'] instanceof UserStatus ? $data['status'] : UserStatus::from($data['status']))
                : null,
            userType: isset($data['user_type'])
                ? ($data['user_type'] instanceof UserType ? $data['user_type'] : UserType::from($data['user_type']))
                : null,
            roles: $data['roles'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }
}
