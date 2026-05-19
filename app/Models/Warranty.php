<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use HasFactory;

    protected $table = 'warranty_records';

    protected $fillable = [
        'user_name', 'mobile_number', 'email', 'purchase_source', 'address', 'product_name', 'serial_number', 'purchase_date', 'expiry_date', 'warranty_status', 'is_deleted', 'created_by', 'modified_by'
    ];
}
