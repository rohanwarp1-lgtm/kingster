<?php

namespace App\Modules\Rma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class RmaTicket extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'ticket_id',
        'customer_name',
        'mobile',
        'email',
        'order_date',
        'order_id',
        'bill_file',
        'product_name',
        'model',
        'platform',
        'issue_type',
        'issue_description',
        'address',
        'replacement_type',
        'assigned_to',
        'sla_deadline',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'sla_deadline' => 'datetime',
    ];

    protected static $logFillable = true;
    protected static $logName = 'rma';
    protected static $logOnlyDirty = true;

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->ticket_id = 'RMA-' . strtoupper(uniqid());
            
            if (!$ticket->sla_deadline) {
                $ticket->sla_deadline = self::calculateSlaDeadline($ticket->issue_type);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('RMA')
            ->logOnlyDirty()
            ->logFillable();
    }

    public static function calculateSlaDeadline(string $issueType): Carbon
    {
        $slaHours = match($issueType) {
            'hardware_defect' => 72,
            'software_issue' => 48,
            'missing_parts' => 24,
            'wrong_item' => 48,
            'damaged' => 36,
            default => 72,
        };

        return now()->addHours($slaHours);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(RmaActivity::class)->orderBy('created_at', 'desc');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(RmaComment::class)->orderBy('created_at', 'desc');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(RmaAttachment::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'open' => '<span class="badge bg-primary">Open</span>',
            'under_review' => '<span class="badge bg-info">Under Review</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            'pickup_pending' => '<span class="badge bg-warning">Pickup Pending</span>',
            'pickup_completed' => '<span class="badge bg-info">Pickup Completed</span>',
            'replacement_shipped' => '<span class="badge bg-success">Replacement Shipped</span>',
            'closed' => '<span class="badge bg-secondary">Closed</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getSlaStatusAttribute(): string
    {
        if ($this->status === 'closed') {
            return '<span class="badge bg-secondary">N/A</span>';
        }

        if ($this->sla_deadline < now()) {
            return '<span class="badge bg-danger">Overdue</span>';
        }

        $hoursRemaining = now()->diffInHours($this->sla_deadline, false);
        if ($hoursRemaining <= 12) {
            return '<span class="badge bg-warning">Urgent</span>';
        }

        return '<span class="badge bg-success">On Track</span>';
    }

    public function scopeFilter($query, $filters)
    {
        return $query->when($filters['status'] ?? null, function($q) use ($filters) {
            $q->where('status', $filters['status']);
        })->when($filters['platform'] ?? null, function($q) use ($filters) {
            $q->where('platform', $filters['platform']);
        })->when($filters['issue_type'] ?? null, function($q) use ($filters) {
            $q->where('issue_type', $filters['issue_type']);
        })->when($filters['assigned_to'] ?? null, function($q) use ($filters) {
            $q->where('assigned_to', $filters['assigned_to']);
        })->when($filters['date_from'] ?? null, function($q) use ($filters) {
            $q->whereDate('order_date', '>=', $filters['date_from']);
        })->when($filters['date_to'] ?? null, function($q) use ($filters) {
            $q->whereDate('order_date', '<=', $filters['date_to']);
        })->when($filters['search'] ?? null, function($q) use ($filters) {
            $q->where(function($query) use ($filters) {
                $query->where('ticket_id', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('customer_name', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('order_id', 'like', '%' . $filters['search'] . '%');
            });
        });
    }
}
