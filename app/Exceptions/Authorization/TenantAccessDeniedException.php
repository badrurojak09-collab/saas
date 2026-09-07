<?php

namespace App\Exceptions\Authorization;

use RuntimeException;

class TenantAccessDeniedException extends RuntimeException
{
    public function __construct(string $message = 'Access to this tenant resource is denied.')
    {
        parent::__construct($message);
    }
}
