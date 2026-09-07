<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\EmploymentStatus;

readonly class CreateLecturerData
{
    public function __construct(
        public ?string $userId,
        public string $employeeNumber,
        public ?string $nidn,
        public string $name,
        public ?string $academicTitle = null,
        public ?string $gender = null,
        public ?string $birthPlace = null,
        public ?string $birthDate = null,
        public ?string $phone = null,
        public ?string $email = null,
        public EmploymentStatus $employmentStatus = EmploymentStatus::Permanent,
        public ?string $joinedAt = null,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            employeeNumber: $data['employee_number'],
            nidn: $data['nidn'] ?? null,
            name: $data['name'],
            academicTitle: $data['academic_title'] ?? null,
            gender: $data['gender'] ?? null,
            birthPlace: $data['birth_place'] ?? null,
            birthDate: $data['birth_date'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            employmentStatus: isset($data['employment_status'])
                ? ($data['employment_status'] instanceof EmploymentStatus ? $data['employment_status'] : EmploymentStatus::from($data['employment_status']))
                : EmploymentStatus::Permanent,
            joinedAt: $data['joined_at'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }
}
