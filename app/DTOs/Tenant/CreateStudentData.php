<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\StudentStatus;

readonly class CreateStudentData
{
    public function __construct(
        public ?string $userId,
        public string $studentNumber,
        public ?string $nationalStudentNumber,
        public string $studyProgramId,
        public int $entryYear,
        public ?string $entrySemesterId,
        public ?string $admissionType,
        public string $name,
        public ?string $gender = null,
        public ?string $birthPlace = null,
        public ?string $birthDate = null,
        public ?string $nik = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $address = null,
        public StudentStatus $status = StudentStatus::Active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            studentNumber: $data['student_number'],
            nationalStudentNumber: $data['national_student_number'] ?? null,
            studyProgramId: $data['study_program_id'],
            entryYear: (int) ($data['entry_year'] ?? date('Y')),
            entrySemesterId: $data['entry_semester_id'] ?? null,
            admissionType: $data['admission_type'] ?? null,
            name: $data['name'],
            gender: $data['gender'] ?? null,
            birthPlace: $data['birth_place'] ?? null,
            birthDate: $data['birth_date'] ?? null,
            nik: $data['nik'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            address: $data['address'] ?? null,
            status: isset($data['status'])
                ? ($data['status'] instanceof StudentStatus ? $data['status'] : StudentStatus::from($data['status']))
                : StudentStatus::Active,
        );
    }
}
