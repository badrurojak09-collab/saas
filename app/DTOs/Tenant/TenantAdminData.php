<?php

namespace App\DTOs\Tenant;

readonly class TenantAdminData
{
    public function __construct(
        public string $name = 'Administrator SIAKAD',
        public string $email = 'admin@tenant.local',
        public ?string $username = 'admin',
        public ?string $password = 'password',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? $data['admin_name'] ?? 'Administrator SIAKAD'),
            email: (string) ($data['email'] ?? $data['admin_email'] ?? 'admin@tenant.local'),
            username: isset($data['username']) || isset($data['admin_username']) ? (string) ($data['username'] ?? $data['admin_username']) : 'admin',
            password: isset($data['password']) || isset($data['admin_password']) ? (string) ($data['password'] ?? $data['admin_password']) : 'password',
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
        ];
    }
}
