<?php

namespace App\Modules\Rma\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RmaCreatedNotification extends Notification implements ShouldQueue
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
            ->subject('RMA Ticket Created - ' . $this->ticket->ticket_id)
            ->greeting('Hello ' . $this->ticket->customer_name . '!')
            ->line('Your replacement/return request has been registered.')
            ->line('Ticket ID: ' . $this->ticket->ticket_id)
            ->line('Product: ' . $this->ticket->product_name)
            ->line('Issue: ' . ucfirst(str_replace('_', ' ', $this->ticket->issue_type)))
            ->line('Status: Open')
            ->action('Track Your Request', url('/rma-status/' . $this->ticket->ticket_id))
            ->line('We will keep you updated on the progress.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->ticket_id,
            'customer_name' => $this->ticket->customer_name,
            'status' => 'open',
        ];
    }
}
