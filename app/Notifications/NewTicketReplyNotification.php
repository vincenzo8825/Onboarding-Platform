<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $reply;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, TicketReply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = $notifiable->role === 'admin' 
            ? route('admin.tickets.show', $this->ticket) 
            : route('employee.tickets.show', $this->ticket);

        return (new MailMessage)
            ->subject('Nuova risposta al ticket: ' . $this->ticket->title)
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('È stata aggiunta una nuova risposta al ticket "' . $this->ticket->title . '".')
            ->line('Da: ' . $this->reply->user->name)
            ->line('Messaggio: ' . \Illuminate\Support\Str::limit($this->reply->message, 100))
            ->action('Visualizza Ticket', url($url))
            ->line('Grazie per l\'utilizzo della nostra piattaforma!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => 'Nuova risposta da ' . $this->reply->user->name,
            'reply_id' => $this->reply->id,
        ];
    }
}