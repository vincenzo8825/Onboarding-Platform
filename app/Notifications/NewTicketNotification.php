<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
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
        return (new MailMessage)
            ->subject('Nuovo ticket di supporto: ' . $this->ticket->title)
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('È stato creato un nuovo ticket di supporto.')
            ->line('Titolo: ' . $this->ticket->title)
            ->line('Priorità: ' . ucfirst($this->ticket->priority))
            ->line('Categoria: ' . $this->ticket->category)
            ->action('Visualizza Ticket', url(route('admin.tickets.show', $this->ticket)))
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
            'message' => 'Nuovo ticket di supporto creato da ' . $this->ticket->creator->name,
            'priority' => $this->ticket->priority,
        ];
    }
}