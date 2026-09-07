<?php

namespace App\Events\Tenant;

use App\Enums\Tenant\StudentStatus;
use App\Models\Tenant\Student;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Student $student,
        public readonly StudentStatus $previousStatus,
        public readonly StudentStatus $newStatus,
        public readonly ?string $reason = null
    ) {}
}
