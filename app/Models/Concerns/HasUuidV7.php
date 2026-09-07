<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuidV7
{
    public function initializeHasUuidV7(): void
    {
        $this->usesUniqueIds = true;
        $this->incrementing = false;
        $this->keyType = 'string';
    }

    public function uniqueIds(): array
    {
        return [$this->getKeyName()];
    }

    public function newUniqueId(): string
    {
        return (string) Str::uuid7();
    }

    protected static function bootHasUuidV7(): void
    {
        static::creating(function (Model $model): void {
            $key = $model->getKeyName();

            if (empty($model->getAttribute($key))) {
                $model->setAttribute($key, $model->newUniqueId());
            }
        });
    }
}
