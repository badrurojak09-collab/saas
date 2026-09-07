<?php

namespace App\Enums\Tenant;

enum TranscriptStatus: string
{
    case Draft = 'draft';
    case Generated = 'generated';
    case Approved = 'approved';
    case Issued = 'issued';
    case Cancelled = 'cancelled';
}
