<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;
use App\Models\TicketReply;

class NewTicketReply extends Notification implements ShouldQueue
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
        $url = $notifiable->hasRole('admin')
            ? route('admin.tickets.show', $this->ticket)
            : route('employee.tickets.show', $this->ticket);

        return (new MailMessage)
            ->subject("Nuova risposta al ticket #{$this->ticket->id}")
            ->greeting("Salve {$notifiable->name},")
            ->line("C'è una nuova risposta al ticket '{$this->ticket->title}'")
            ->line("Messaggio: " . \Illuminate\Support\Str::limit($this->reply->message, 150))
            ->action('Visualizza Ticket', $url)
            ->line('Grazie per l\'utilizzo della nostra piattaforma di onboarding!');
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
            'reply_id' => $this->reply->id,
            'user_id' => $this->reply->user_id,
            'title' => $this->ticket->title,
            'message' => \Illuminate\Support\Str::limit($this->reply->message, 100),
            'type' => 'ticket_reply',
        ];
    }
}
