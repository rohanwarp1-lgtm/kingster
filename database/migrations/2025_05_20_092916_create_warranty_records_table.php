<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warranty_records', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->string('mobile_number', 15);
            $table->string('email')->nullable();
            $table->string('purchase_source');
            $table->text('address')->nullable();
            $table->string('product_name');
            $table->string('serial_number')->unique();
            $table->date('purchase_date');
            $table->date('expiry_date');
            $table->enum('warranty_status', ['Pending', 'Active', 'Expired', 'Rejected'])->default('Pending');
            $table->integer('created_by')->nullable()->default(null);
            $table->integer('modified_by')->nullable()->default(null);
            $table->integer('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_records');
    }
};
