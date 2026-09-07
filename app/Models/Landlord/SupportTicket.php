<?php

namespace App\Models\Landlord;

use App\Enums\Landlord\SupportTicketPriority;
use App\Enums\Landlord\SupportTicketStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends LandlordModel
{
    use SoftDeletes;

    protected $table = 'support_tickets';

    protected $fillable = [
        'tenant_id',
        'ticket_number',
        'subject',
        'priority',
        'status',
        'created_by',
        'assigned_to',
        'resolved_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'priority' => SupportTicketPriority::class,
            'status' => SupportTicketStatus::class,
            'resolved_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(PlatformUser::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(PlatformUser::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }
}
