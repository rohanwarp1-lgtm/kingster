<?php

namespace App\Modules\Rma\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RmaClosedNotification extends Notification implements ShouldQueue
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
            ->subject('RMA Request Completed - ' . $this->ticket->ticket_id)
            ->greeting('Hello ' . $this->ticket->customer_name . '!')
            ->line('Your replacement/return request has been completed.')
            ->line('Ticket ID: ' . $this->ticket->ticket_id)
            ->line('Product: ' . $this->ticket->product_name)
            ->line('Final Status: Closed')
            ->action('View History', url('/rma-status/' . $this->ticket->ticket_id))
            ->line('Thank you for your patience. We hope to serve you better in the future.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->ticket_id,
            'status' => 'closed',
        ];
    }
}
