<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\AuditLog;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => 'created',
            'resource_type' => 'Student',
            'resource_id' => (string) Str::uuid(),
            'request_id' => (string) Str::uuid(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console/Testing',
            'before' => null,
            'after' => ['status' => 'active'],
            'metadata' => ['env' => 'testing'],
        ];
    }
}
