<?php

namespace App\Modules\Rma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RmaActivity extends Model
{
    protected $fillable = [
        'rma_ticket_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
        'notes',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(RmaTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Ticket Created',
            'status_changed' => 'Status Changed',
            'assigned' => 'Assigned to Staff',
            'unassigned' => 'Unassigned',
            'comment' => 'Comment Added',
            'attachment' => 'Attachment Added',
            'note' => 'Internal Note',
            'sla_warning' => 'SLA Warning',
            'sla_breach' => 'SLA Breach',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public function getIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'plus-circle',
            'status_changed' => 'refresh-cw',
            'assigned' => 'user-plus',
            'unassigned' => 'user-minus',
            'comment' => 'message-circle',
            'attachment' => 'paperclip',
            'note' => 'file-text',
            'sla_warning' => 'alert-triangle',
            'sla_breach' => 'alert-octagon',
            default => 'activity',
        };
    }
}
