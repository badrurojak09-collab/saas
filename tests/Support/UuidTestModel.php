<?php

namespace Tests\Support;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Model;

class UuidTestModel extends Model
{
    use HasUuidV7;

    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'uuid_test_models';

    public function triggerCreating(): bool
    {
        return $this->fireModelEvent('creating') !== false;
    }
}
