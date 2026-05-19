<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_reports', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('product_name');
            $table->string('model_name');
            $table->enum('marketplace', ['amazon', 'flipkart', 'other'])->default('other');
            $table->string('return_reason');
            $table->enum('refund_status', ['pending', 'processed', 'rejected', 'partial'])->default('pending');
            $table->decimal('return_cost', 10, 2)->default(0);
            $table->decimal('loss_amount', 10, 2)->default(0);
            $table->string('warehouse');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['order_id', 'marketplace']);
            $table->index(['warehouse', 'return_reason']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_reports');
    }
};
