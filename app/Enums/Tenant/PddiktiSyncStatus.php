<?php

namespace App\Enums\Tenant;

enum PddiktiSyncStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Success = 'success';
    case Failed = 'failed';
}
