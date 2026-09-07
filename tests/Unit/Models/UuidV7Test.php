<?php

namespace Tests\Unit\Models;

use Illuminate\Support\Str;
use Tests\Support\UuidTestModel;
use Tests\TestCase;

class UuidV7Test extends TestCase
{
    public function test_uuid_v7_is_valid_and_version_7(): void
    {
        $uuid = (string) Str::uuid7();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
        );
    }

    public function test_generated_uuids_are_unique(): void
    {
        $ids = collect(range(1, 50))->map(fn () => (string) Str::uuid7());

        $this->assertCount(50, $ids->unique());
    }

    public function test_model_key_is_uuid_string_and_not_incrementing(): void
    {
        $model = new UuidTestModel;

        $this->assertFalse($model->getIncrementing());
        $this->assertSame('string', $model->getKeyType());
        $this->assertSame('id', $model->getKeyName());
    }

    public function test_creating_event_assigns_uuid_v7(): void
    {
        $model = new UuidTestModel;

        $this->assertNull($model->getKey());

        $model->triggerCreating();

        $this->assertNotEmpty($model->getKey());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            (string) $model->getKey(),
        );
    }

    public function test_existing_key_is_not_overwritten(): void
    {
        $existing = (string) Str::uuid7();
        $model = new UuidTestModel;
        $model->setAttribute('id', $existing);

        $model->triggerCreating();

        $this->assertSame($existing, $model->getKey());
    }
}
