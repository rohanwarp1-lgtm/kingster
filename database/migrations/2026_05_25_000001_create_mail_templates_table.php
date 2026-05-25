<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->string('name');
            $table->string('subject');
            $table->longText('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('mail_templates')->insert([
            'type'    => 'warranty_registration',
            'name'    => 'Warranty Registration Confirmation',
            'subject' => 'Warranty Registration Received – Ticket #{ticket_no}',
            'body'    => '<p>Dear <strong>{customer_name}</strong>,</p>
<p>Thank you for registering your product warranty with <strong>Kingster</strong>. We are pleased to confirm that we have successfully received your warranty registration request.</p>
<p>Our dedicated support team is currently reviewing your submission and will activate your warranty as soon as possible. You will receive a separate notification via email once your warranty has been fully activated.</p>
<p>In the meantime, you can use your ticket number <strong>{ticket_no}</strong> to track your warranty status at any time.</p>
<p>If you have any questions or need assistance, feel free to reach out to us at <a href="mailto:support@kingster.info" style="color:#667eea;">support@kingster.info</a>. We are always happy to help.</p>
<p style="margin-top:24px;">Warm regards,<br><strong>Kingster Support Team</strong></p>',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};
