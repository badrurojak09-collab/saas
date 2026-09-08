<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | The landlord connection is the control plane database holding tenants,
    | domains, packages, and subscriptions. The tenant connection is configured
    | dynamically per request/job.
    |
    */

    'landlord_connection' => env('LANDLORD_DB_CONNECTION', 'landlord'),

    'tenant_connection' => env('TENANT_DB_CONNECTION', 'tenant'),

    'panel_domain' => env('TENANT_PANEL_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Domain Resolution
    |--------------------------------------------------------------------------
    |
    | When enabled, TenantResolver inspects the HTTP request host to match
    | an active, verified domain in the landlord database.
    |
    */

    'domain_resolution' => [
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Resolution Cache
    |--------------------------------------------------------------------------
    |
    | Caches domain-to-tenant_id mapping to avoid unnecessary Landlord DB
    | queries on every request. Passwords or credentials are NEVER cached.
    |
    */

    'cache' => [
        'enabled' => env('TENANCY_CACHE_ENABLED', true),
        'ttl' => env('TENANCY_CACHE_TTL', 300), // seconds
        'prefix' => 'tenancy.domain.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Strict Tenancy Mode
    |--------------------------------------------------------------------------
    |
    | When strict mode is enabled, querying any TenantModel without an active,
    | initialized tenant context throws a TenantContextMissingException immediately.
    | Fallback to landlord DB or default DB is strictly forbidden.
    |
    */

    'strict' => env('TENANCY_STRICT_MODE', true),

];
