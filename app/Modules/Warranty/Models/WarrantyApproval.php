<?php

namespace App\Modules\Warranty\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarrantyApproval extends Model
{
    protected $fillable = [
        'warranty_id',
        'approver_id',
        'action',
        'notes',
        'ip_address',
    ];

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(WarrantyRegistration::class, 'warranty_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id');
    }

    public function getActionBadgeAttribute(): string
    {
        $badges = [
            'submitted' => '<span class="badge bg-primary">Submitted</span>',
            'under_review' => '<span class="badge bg-info">Under Review</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
        ];

        return $badges[$this->action] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}
