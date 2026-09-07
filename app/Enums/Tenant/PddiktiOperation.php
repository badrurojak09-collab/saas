<?php

namespace App\Enums\Tenant;

enum PddiktiOperation: string
{
    case Insert = 'insert';
    case Update = 'update';
    case Delete = 'delete';
}
