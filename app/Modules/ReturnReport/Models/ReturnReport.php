<?php

namespace App\Modules\ReturnReport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ReturnReport extends Model
{
    use SoftDeletes, LogsActivity, HasFactory;

    protected $fillable = [
        'order_id',
        'product_name',
        'model_name',
        'marketplace',
        'return_reason',
        'refund_status',
        'return_cost',
        'loss_amount',
        'warehouse',
    ];

    protected $casts = [
        'return_cost' => 'decimal:2',
        'loss_amount' => 'decimal:2',
    ];

    protected static $logFillable = true;
    protected static $logName = 'return_report';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Return Report')
            ->logFillable()
            ->logOnlyDirty();
    }

    public function scopeFilter($query, $filters)
    {
        return $query->when($filters['marketplace'] ?? null, function($q) use ($filters) {
            $q->where('marketplace', $filters['marketplace']);
        })->when($filters['warehouse'] ?? null, function($q) use ($filters) {
            $q->where('warehouse', $filters['warehouse']);
        })->when($filters['refund_status'] ?? null, function($q) use ($filters) {
            $q->where('refund_status', $filters['refund_status']);
        })->when($filters['return_reason'] ?? null, function($q) use ($filters) {
            $q->where('return_reason', $filters['return_reason']);
        })->when($filters['date_from'] ?? null, function($q) use ($filters) {
            $q->whereDate('created_at', '>=', $filters['date_from']);
        })->when($filters['date_to'] ?? null, function($q) use ($filters) {
            $q->whereDate('created_at', '<=', $filters['date_to']);
        })->when($filters['search'] ?? null, function($q) use ($filters) {
            $q->where(function($query) use ($filters) {
                $query->where('order_id', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('product_name', 'like', '%' . $filters['search'] . '%');
            });
        });
    }
}
