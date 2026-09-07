<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\PddiktiSyncStatus;
use App\Models\Tenant\PddiktiMapping;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PddiktiMappingFactory extends Factory
{
    protected $model = PddiktiMapping::class;

    public function definition(): array
    {
        return [
            'entity_type' => 'student',
            'entity_id' => (string) Str::uuid(),
            'external_id' => (string) Str::uuid(),
            'external_code' => strtoupper(fake()->bothify('PDD-#####')),
            'sync_status' => PddiktiSyncStatus::Pending,
            'last_synced_at' => null,
            'metadata' => [],
        ];
    }
}
