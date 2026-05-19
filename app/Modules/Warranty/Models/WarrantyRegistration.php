<?php

namespace App\Modules\Warranty\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WarrantyRegistration extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'ticket_no',
        'customer_name',
        'mobile',
        'email',
        'product_name',
        'model',
        'serial_number',
        'price',
        'purchase_date',
        'purchase_platform',
        'order_id',
        'warranty_type',
        'invoice_file',
        'status',
        'approval_notes',
        'expiry_date',
        'approved_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'price' => 'decimal:2',
    ];

    protected static $logFillable = true;
    protected static $logName = 'warranty';
    protected static $logOnlyDirty = true;

    protected static function booted()
    {
        static::creating(function ($warranty) {
            $warranty->ticket_no = 'WARR-' . strtoupper(uniqid());
            
            if (!$warranty->expiry_date) {
                $warranty->expiry_date = $warranty->purchase_date->addYear();
            }
            
            if (!$warranty->warranty_type) {
                $warranty->warranty_type = 'standard';
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Warranty')
            ->logOnlyDirty()
            ->logFillable();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(WarrantyApproval::class, 'warranty_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'under_review' => '<span class="badge bg-info">Under Review</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            'expired' => '<span class="badge bg-dark">Expired</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->price, 2);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date < now();
    }

    public function getWarrantyStatusAttribute(): string
    {
        if ($this->status === 'cancelled' || $this->status === 'rejected') {
            return $this->status;
        }
        
        if ($this->is_expired) {
            return 'expired';
        }
        
        return $this->status;
    }

    public function scopeFilter($query, $filters)
    {
        return $query->when($filters['status'] ?? null, function($q) use ($filters) {
            $q->where('status', $filters['status']);
        })->when($filters['platform'] ?? null, function($q) use ($filters) {
            $q->where('purchase_platform', $filters['platform']);
        })->when($filters['warranty_type'] ?? null, function($q) use ($filters) {
            $q->where('warranty_type', $filters['warranty_type']);
        })->when($filters['date_from'] ?? null, function($q) use ($filters) {
            $q->whereDate('purchase_date', '>=', $filters['date_from']);
        })->when($filters['date_to'] ?? null, function($q) use ($filters) {
            $q->whereDate('purchase_date', '<=', $filters['date_to']);
        })->when($filters['search'] ?? null, function($q) use ($filters) {
            $q->where(function($query) use ($filters) {
                $query->where('ticket_no', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('customer_name', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('product_name', 'like', '%' . $filters['search'] . '%');
            });
        });
    }
}
