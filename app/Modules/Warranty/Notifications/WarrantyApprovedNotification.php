<?php

namespace App\Modules\Warranty\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WarrantyApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private $warranty
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Warranty Approved - ' . $this->warranty->ticket_no)
            ->greeting('Hello ' . $this->warranty->customer_name . '!')
            ->line('Great news! Your warranty registration has been approved.')
            ->line('Ticket Number: ' . $this->warranty->ticket_no)
            ->line('Product: ' . $this->warranty->product_name)
            ->line('Warranty Valid Until: ' . $this->warranty->expiry_date->format('d M Y'))
            ->action('View Details', url('/warranty-status-lookup'))
            ->line('Please keep this information for your records.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_no' => $this->warranty->ticket_no,
            'customer_name' => $this->warranty->customer_name,
            'status' => 'approved',
            'expiry_date' => $this->warranty->expiry_date->toDateString(),
        ];
    }
}
