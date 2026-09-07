<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\UserStatus;
use App\Enums\Tenant\UserType;
use InvalidArgumentException;

readonly class CreateTenantUserData
{
    public function __construct(
        public string $name,
        public ?string $email = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?string $phone = null,
        public ?string $avatarPath = null,
        public UserStatus $status = UserStatus::Active,
        public UserType $userType = UserType::Admin,
        public array $roles = [],
        public array $metadata = [],
    ) {
        if (blank($this->email) && blank($this->username)) {
            throw new InvalidArgumentException('Either email or username must be provided.');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'] ?? null,
            username: $data['username'] ?? null,
            password: $data['password'] ?? null,
            phone: $data['phone'] ?? null,
            avatarPath: $data['avatar_path'] ?? null,
            status: isset($data['status'])
                ? ($data['status'] instanceof UserStatus ? $data['status'] : UserStatus::from($data['status']))
                : UserStatus::Active,
            userType: isset($data['user_type'])
                ? ($data['user_type'] instanceof UserType ? $data['user_type'] : UserType::from($data['user_type']))
                : UserType::Admin,
            roles: $data['roles'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }
}
