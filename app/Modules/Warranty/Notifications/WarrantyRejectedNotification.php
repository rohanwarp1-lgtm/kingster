<?php

namespace App\Modules\Warranty\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WarrantyRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private $warranty,
        private ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Warranty Registration Update - ' . $this->warranty->ticket_no)
            ->greeting('Hello ' . $this->warranty->customer_name . '!')
            ->line('Unfortunately, your warranty registration could not be approved at this time.')
            ->line('Ticket Number: ' . $this->warranty->ticket_no)
            ->line('Product: ' . $this->warranty->product_name);

        if ($this->reason) {
            $mail->line('Reason: ' . $this->reason);
        }

        $mail->line('If you believe this is an error, please contact our support team.')
             ->action('Contact Support', url('/contact-us'));

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_no' => $this->warranty->ticket_no,
            'customer_name' => $this->warranty->customer_name,
            'status' => 'rejected',
            'reason' => $this->reason,
        ];
    }
}
