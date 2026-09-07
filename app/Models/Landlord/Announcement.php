<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\AnnouncementStatus;
use App\Enums\Landlord\AnnouncementType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends LandlordModel
{
    use SoftDeletes;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'content',
        'type',
        'status',
        'published_at',
        'expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => AnnouncementType::class,
            'status' => AnnouncementStatus::class,
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(PlatformUser::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AnnouncementRecipient::class, 'announcement_id');
    }
}
