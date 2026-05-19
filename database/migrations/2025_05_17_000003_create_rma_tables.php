<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rma_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->unique();
            $table->string('customer_name');
            $table->string('mobile', 20);
            $table->string('email');
            $table->date('order_date');
            $table->string('order_id');
            $table->string('bill_file')->nullable();
            $table->string('product_name');
            $table->string('model');
            $table->enum('platform', ['amazon', 'flipkart', 'other'])->default('other');
            $table->enum('issue_type', ['hardware_defect', 'software_issue', 'missing_parts', 'wrong_item', 'damaged', 'other'])->default('other');
            $table->text('issue_description');
            $table->text('address');
            $table->enum('replacement_type', ['full', 'partial', 'refund'])->default('full');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('sla_deadline');
            $table->enum('status', [
                'open',
                'under_review',
                'approved',
                'rejected',
                'pickup_pending',
                'pickup_completed',
                'replacement_shipped',
                'closed'
            ])->default('open');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ticket_id', 'status']);
            $table->index(['email', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('sla_deadline');
        });

        Schema::create('rma_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rma_ticket_id')->constrained('rma_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['rma_ticket_id', 'created_at']);
        });

        Schema::create('rma_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rma_ticket_id')->constrained('rma_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->text('content');
            $table->boolean('is_internal')->default(false);
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('rma_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rma_ticket_id')->constrained('rma_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rma_attachments');
        Schema::dropIfExists('rma_comments');
        Schema::dropIfExists('rma_activities');
        Schema::dropIfExists('rma_tickets');
    }
};
