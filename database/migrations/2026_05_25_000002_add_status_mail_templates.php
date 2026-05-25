<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('mail_templates')->upsert([
            [
                'type'       => 'warranty_active',
                'name'       => 'Warranty Activated',
                'subject'    => 'Your Warranty is Now Active – {ticket_no}',
                'body'       => '<p>Dear <strong>{customer_name}</strong>,</p>
<p>We are excited to inform you that your warranty registration has been <strong>approved and activated</strong>! Your product is now fully covered under the Kingster Warranty Program.</p>
<p>Your warranty is valid until <strong>{expiry_date}</strong>. Please keep your ticket number <strong>{ticket_no}</strong> safe for any future claims or support requests.</p>
<p>Should you need any assistance during the warranty period, our support team is always here to help at <a href="mailto:support@kingster.info" style="color:#28c76f;">support@kingster.info</a>.</p>
<p style="margin-top:24px;">With warm regards,<br><strong>Kingster Support Team</strong></p>',
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type'       => 'warranty_rejected',
                'name'       => 'Warranty Rejected',
                'subject'    => 'Update on Your Warranty Registration – {ticket_no}',
                'body'       => '<p>Dear <strong>{customer_name}</strong>,</p>
<p>Thank you for submitting your warranty registration with Kingster. After reviewing your application (Ticket: <strong>{ticket_no}</strong>), we regret to inform you that we were <strong>unable to approve</strong> your warranty registration at this time.</p>
<p>If a reason was provided: <strong>{reason}</strong></p>
<p>If you believe this decision was made in error, or if you have additional documentation that may support your application, please do not hesitate to contact our support team at <a href="mailto:support@kingster.info" style="color:#ea5455;">support@kingster.info</a>. We will be happy to review your case.</p>
<p style="margin-top:24px;">Regards,<br><strong>Kingster Support Team</strong></p>',
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type'       => 'warranty_expired',
                'name'       => 'Warranty Expired',
                'subject'    => 'Your Warranty Has Expired – {ticket_no}',
                'body'       => '<p>Dear <strong>{customer_name}</strong>,</p>
<p>This is a notification to let you know that your Kingster warranty (Ticket: <strong>{ticket_no}</strong>) for <strong>{product_name}</strong> has <strong>expired</strong> as of <strong>{expiry_date}</strong>.</p>
<p>We hope that your product has served you well during the warranty period. If you are experiencing any issues with your product, our support team is still available to assist you.</p>
<p>For extended coverage or future product inquiries, please feel free to reach out to us at <a href="mailto:support@kingster.info" style="color:#74788d;">support@kingster.info</a>.</p>
<p style="margin-top:24px;">Thank you for choosing Kingster.<br><strong>Kingster Support Team</strong></p>',
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['type'], ['name', 'subject', 'body', 'is_active', 'updated_at']);
    }

    public function down(): void
    {
        DB::table('mail_templates')->whereIn('type', ['warranty_active', 'warranty_rejected', 'warranty_expired'])->delete();
    }
};
