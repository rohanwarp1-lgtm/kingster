<?php

namespace App\Modules\Rma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RmaComment extends Model
{
    protected $fillable = [
        'rma_ticket_id',
        'user_id',
        'content',
        'is_internal',
        'ip_address',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(RmaTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->is_internal ? 'Internal Note' : 'Comment';
    }
}
