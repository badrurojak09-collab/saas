<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\PlatformUserStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;  // Interface Authorizable
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;  // Trait Authorizable
use Spatie\Permission\Traits\HasRoles;

class PlatformUser extends LandlordModel implements AuthenticatableContract, AuthorizableContract, FilamentUser
{
    use Authenticatable;
    use Authorizable;  // Trait ini wajib ada
    use HasRoles;  // Trait dari Spatie
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'platform_users';

    protected $guard_name = 'platform';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'status',
        'email_verified_at',
        'last_login_at',
        'metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => PlatformUserStatus::class,
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'metadata' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === PlatformUserStatus::ACTIVE;
    }

    public function createdTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'created_by');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }
}
