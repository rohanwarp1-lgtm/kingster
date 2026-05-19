<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fba_autos', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_id')->unique();
            $table->date('shipment_date');
            $table->time('shipment_time');
            $table->string('product_name');
            $table->unsignedInteger('qty');
            $table->string('state');
            $table->string('warehouse_name');
            $table->decimal('qty_price', 10, 2);
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', [
                'pending',
                'processing',
                'shipped',
                'delivered',
                'closed',
                'cancelled',
                'returned'
            ])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['warehouse_name', 'state']);
            $table->index('shipment_date');
            $table->index('status');
            $table->index('product_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fba_autos');
    }
};
