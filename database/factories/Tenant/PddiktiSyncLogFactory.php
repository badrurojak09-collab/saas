<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\PddiktiOperation;
use App\Models\Tenant\PddiktiSyncLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PddiktiSyncLogFactory extends Factory
{
    protected $model = PddiktiSyncLog::class;

    public function definition(): array
    {
        return [
            'entity_type' => 'student',
            'entity_id' => (string) Str::uuid(),
            'operation' => PddiktiOperation::Insert,
            'request_id' => (string) Str::uuid(),
            'status' => 'success',
            'request_payload' => ['sample' => 'data'],
            'response_payload' => ['status' => 200],
            'error_message' => null,
            'started_at' => now(),
            'completed_at' => now(),
        ];
    }
}
