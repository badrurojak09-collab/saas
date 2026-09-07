<?php

namespace App\Events\Tenant;

use App\Models\Tenant\Curriculum;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CurriculumPublished
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Curriculum $curriculum
    ) {}
}
