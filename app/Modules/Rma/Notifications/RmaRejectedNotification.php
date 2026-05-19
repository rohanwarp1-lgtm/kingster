<?php

namespace App\Modules\Rma\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RmaRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private $ticket
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('RMA Request Update - ' . $this->ticket->ticket_id)
            ->greeting('Hello ' . $this->ticket->customer_name . '!')
            ->line('We regret to inform you that your replacement/return request has been rejected.')
            ->line('Ticket ID: ' . $this->ticket->ticket_id)
            ->line('Product: ' . $this->ticket->product_name)
            ->action('View Details', url('/rma-status/' . $this->ticket->ticket_id))
            ->line('If you have questions, please contact our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->ticket_id,
            'status' => 'rejected',
        ];
    }
}
