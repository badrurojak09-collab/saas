<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTypeFactory extends Factory
{
    protected $model = DocumentType::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('DOC-###')),
            'name' => 'Dokumen ' . fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'entity_type' => 'student',
            'is_required' => true,
            'status' => 'active',
        ];
    }
}
