<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductName extends Model
{
    use HasFactory;

    protected $table = 'product_names';
    protected $fillable = ['name', 'is_deleted', 'created_by', 'modified_by'];
}
