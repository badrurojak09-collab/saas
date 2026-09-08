<?php

namespace App\Enums\Tenant;

enum OrganizationMembershipType: string
{
    case Member = 'member';
    case Manager = 'manager';
    case Head = 'head';
    case Secretary = 'secretary';
    case Coordinator = 'coordinator';
}
