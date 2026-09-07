<?php

namespace App\Models\Tenant;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class UserInvitation extends TenantModel
{
    protected $table = 'user_invitations';

    protected $fillable = [
        'email',
        'token_hash',
        'roles',
        'invited_by',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'roles' => 'array',
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isUsable(): bool
    {
        return $this->status === 'pending'
            && $this->expires_at instanceof CarbonInterface
            && $this->expires_at->isFuture();
    }

    public static function issue(string $email, ?User $inviter = null, array $roles = [], int $days = 3): array
    {
        $plainToken = Str::random(64);
        $invitation = self::query()->create([
            'email' => strtolower(trim($email)),
            'token_hash' => hash('sha256', $plainToken),
            'roles' => array_values(array_unique($roles)),
            'invited_by' => $inviter?->getKey(),
            'expires_at' => now()->addDays($days),
        ]);

        return [$invitation, $plainToken];
    }
}
