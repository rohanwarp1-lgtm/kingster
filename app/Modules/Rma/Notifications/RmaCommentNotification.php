<?php

namespace App\Modules\Rma\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RmaCommentNotification extends Notification implements ShouldQueue
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
            ->subject('New Update on Your Request - ' . $this->ticket->ticket_id)
            ->greeting('Hello ' . $this->ticket->customer_name . '!')
            ->line('There is a new update on your replacement/return request.')
            ->line('Ticket ID: ' . $this->ticket->ticket_id)
            ->line('Current Status: ' . ucfirst(str_replace('_', ' ', $this->ticket->status)))
            ->action('View Details', url('/rma-status/' . $this->ticket->ticket_id))
            ->line('Please check for any comments or requirements from our team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->ticket_id,
            'status' => 'updated',
        ];
    }
}
