<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\UserStatus;
use App\Enums\Tenant\UserType;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends TenantModel implements AuthenticatableContract, AuthorizableContract, FilamentUser
{
    use Authenticatable;
    use Authorizable;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'users';

    protected $guard_name = 'tenant';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'phone',
        'avatar_path',
        'status',
        'user_type',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => UserStatus::class,
            'user_type' => UserType::class,
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'metadata' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'tenant'
            && $this->status === UserStatus::Active;
    }

    public function lecturer(): HasOne
    {
        return $this->hasOne(Lecturer::class, 'user_id');
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(UserInvitation::class, 'invited_by');
    }

    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class, 'user_id');
    }
}
