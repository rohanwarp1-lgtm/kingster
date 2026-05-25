<?php

namespace App\Modules\Warranty\Notifications;

use App\Models\MailTemplate;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WarrantyRejectedNotification extends Notification
{
    public function __construct(private $warranty, private ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $template = MailTemplate::getTemplate('warranty_rejected');

        if ($template) {
            ['subject' => $subject, 'body' => $body] = $template->render([
                'customer_name' => $this->warranty->customer_name,
                'ticket_no'     => $this->warranty->ticket_no,
                'product_name'  => $this->warranty->product_name,
                'model'         => $this->warranty->model ?? '',
                'serial_number' => $this->warranty->serial_number ?? '',
                'purchase_date' => optional($this->warranty->purchase_date)->format('d M Y') ?? '',
                'expiry_date'   => optional($this->warranty->expiry_date)->format('d M Y') ?? '',
                'warranty_type' => ucfirst($this->warranty->warranty_type ?? 'standard'),
                'reason'        => $this->reason ?? 'No reason provided',
            ]);
        } else {
            $subject = 'Update on Your Warranty – ' . $this->warranty->ticket_no;
            $body    = '<p>Dear <strong>' . e($this->warranty->customer_name) . '</strong>, unfortunately your warranty was not approved.</p>';
        }

        return (new MailMessage)->subject($subject)->view('emails.warranty.status-update', [
            'subject'     => $subject,
            'body'        => $body,
            'warranty'    => $this->warranty,
            'status'      => 'rejected',
            'headerTitle' => 'Warranty Not Approved',
        ]);
    }
}
