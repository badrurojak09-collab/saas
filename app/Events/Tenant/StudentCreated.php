<?php

namespace App\Events\Tenant;

use App\Models\Tenant\Student;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Student $student
    ) {}
}
