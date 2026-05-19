<?php

namespace App\Modules\Rma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RmaAttachment extends Model
{
    protected $fillable = [
        'rma_ticket_id',
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(RmaTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
