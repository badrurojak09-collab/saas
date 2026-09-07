<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\DocumentStatus;
use App\Models\Tenant\DocumentType;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudentDocument;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentDocumentFactory extends Factory
{
    protected $model = StudentDocument::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'document_type_id' => DocumentType::factory(),
            'file_name' => 'document_' . fake()->uuid() . '.pdf',
            'file_path' => 'documents/' . fake()->uuid() . '.pdf',
            'disk' => 's3',
            'mime_type' => 'application/pdf',
            'file_size' => 1024 * fake()->numberBetween(50, 5000),
            'checksum' => hash('sha256', fake()->text()),
            'uploaded_by' => User::factory(),
            'verified_by' => null,
            'verified_at' => null,
            'status' => DocumentStatus::Pending,
        ];
    }
}
