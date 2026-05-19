<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique();
            $table->string('customer_name');
            $table->string('mobile', 20);
            $table->string('email');
            $table->string('product_name');
            $table->string('model');
            $table->string('serial_number');
            $table->decimal('price', 10, 2);
            $table->date('purchase_date');
            $table->string('purchase_platform');
            $table->string('order_id');
            $table->string('invoice_file')->nullable();
            $table->enum('warranty_type', ['standard', 'extended', 'premium'])->default('standard');
            $table->enum('status', [
                'pending',
                'under_review',
                'approved',
                'rejected',
                'expired',
                'cancelled'
            ])->default('pending');
            $table->text('approval_notes')->nullable();
            $table->date('expiry_date');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ticket_no', 'status']);
            $table->index(['email', 'status']);
            $table->index('expiry_date');
        });

        Schema::create('warranty_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_id')->constrained('warranty_registrations')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action', ['submitted', 'under_review', 'approved', 'rejected']);
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_approvals');
        Schema::dropIfExists('warranty_registrations');
    }
};
