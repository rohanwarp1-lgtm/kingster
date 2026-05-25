<?php

namespace App\Modules\FbaAuto\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class FbaAuto extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'shipment_id',
        'shipment_date',
        'product_name',
        'asin',
        'sku',
        'sku_label',
        'qty',
        'state',
        'warehouse_name',
        'qty_price',
        'generated_by',
        'updated_by',
        'status',
    ];

    protected $casts = [
        'shipment_date' => 'date',
        'qty_price' => 'decimal:2', // stored as decimal(15,2) — max ₹9,999,999,999,999.99
        'qty' => 'integer',
    ];

    protected static $logFillable = true;
    protected static $logName = 'fba_auto';
    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('FBA Shipment')
            ->logOnlyDirty()
            ->logFillable();
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'generated_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'processing' => '<span class="badge bg-info">Processing</span>',
            'shipped' => '<span class="badge bg-primary">Shipped</span>',
            'delivered' => '<span class="badge bg-success">Delivered</span>',
            'closed' => '<span class="badge bg-secondary">Closed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'returned' => '<span class="badge bg-dark">Returned</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->qty_price, 2);
    }

    public function scopeFilter($query, $filters)
    {
        return $query->when($filters['warehouse'] ?? null, function($q) use ($filters) {
            $q->where('warehouse_name', $filters['warehouse']);
        })->when($filters['state'] ?? null, function($q) use ($filters) {
            $q->where('state', $filters['state']);
        })->when($filters['status'] ?? null, function($q) use ($filters) {
            $q->where('status', $filters['status']);
        })->when($filters['date_from'] ?? null, function($q) use ($filters) {
            $q->whereDate('shipment_date', '>=', $filters['date_from']);
        })->when($filters['date_to'] ?? null, function($q) use ($filters) {
            $q->whereDate('shipment_date', '<=', $filters['date_to']);
        })->when($filters['search'] ?? null, function($q) use ($filters) {
            $q->where(function($query) use ($filters) {
                $query->where('shipment_id', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('product_name', 'like', '%' . $filters['search'] . '%');
            });
        });
    }
}
