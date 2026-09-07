<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\DegreeLevel;

readonly class CreateStudyProgramData
{
    public function __construct(
        public string $departmentId,
        public string $code,
        public string $name,
        public DegreeLevel $degreeLevel = DegreeLevel::S1,
        public ?string $accreditationStatus = null,
        public ?string $accreditationNumber = null,
        public ?string $accreditationExpiredAt = null,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            departmentId: $data['department_id'],
            code: $data['code'],
            name: $data['name'],
            degreeLevel: isset($data['degree_level'])
                ? ($data['degree_level'] instanceof DegreeLevel ? $data['degree_level'] : DegreeLevel::from($data['degree_level']))
                : DegreeLevel::S1,
            accreditationStatus: $data['accreditation_status'] ?? null,
            accreditationNumber: $data['accreditation_number'] ?? null,
            accreditationExpiredAt: $data['accreditation_expired_at'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }
}
