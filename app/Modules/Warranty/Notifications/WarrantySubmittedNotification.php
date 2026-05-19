<?php

namespace App\Modules\Warranty\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WarrantySubmittedNotification extends Notification implements ShouldQueue
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
            ->subject('Warranty Registration Submitted - ' . $this->warranty->ticket_no)
            ->greeting('Hello ' . $this->warranty->customer_name . '!')
            ->line('Your warranty registration has been submitted successfully.')
            ->line('Ticket Number: ' . $this->warranty->ticket_no)
            ->line('Product: ' . $this->warranty->product_name)
            ->line('Warranty Type: ' . ucfirst($this->warranty->warranty_type))
            ->line('Expiry Date: ' . $this->warranty->expiry_date->format('d M Y'))
            ->action('Track Your Warranty', url('/warranty-status-lookup'))
            ->line('We will review your registration and notify you once approved.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_no' => $this->warranty->ticket_no,
            'customer_name' => $this->warranty->customer_name,
            'product_name' => $this->warranty->product_name,
            'status' => 'submitted',
        ];
    }
}
