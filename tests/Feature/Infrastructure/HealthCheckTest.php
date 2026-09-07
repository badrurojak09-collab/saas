<?php

namespace Tests\Feature\Infrastructure;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_expected_payload(): void
    {
        $response = $this->getJson('/health');

        $response->assertJsonStructure([
            'status',
            'application',
            'php',
            'laravel',
            'database',
            'tenant_database',
            'redis',
            'queue',
            'storage',
        ]);

        $payload = $response->json();

        $this->assertSame('skipped', $payload['redis']);
        $this->assertSame('configured', $payload['tenant_database']);
        $this->assertStringNotContainsString('password', strtolower($response->getContent()));

        if ($payload['database'] === 'ok') {
            $response->assertOk();
            $this->assertSame('ok', $payload['status']);
        } else {
            $response->assertStatus(503);
            $this->assertSame('error', $payload['status']);
        }
    }
}
