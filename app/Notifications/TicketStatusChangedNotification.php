<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, string $oldStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
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
        $statusMap = [
            'open' => 'Aperto',
            'in_progress' => 'In lavorazione',
            'resolved' => 'Risolto',
            'closed' => 'Chiuso'
        ];

        return (new MailMessage)
            ->subject('Aggiornamento stato ticket: ' . $this->ticket->title)
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Lo stato del tuo ticket "' . $this->ticket->title . '" è stato aggiornato.')
            ->line('Da: ' . ($statusMap[$this->oldStatus] ?? $this->oldStatus))
            ->line('A: ' . ($statusMap[$this->ticket->status] ?? $this->ticket->status))
            ->action('Visualizza Ticket', url(route('employee.tickets.show', $this->ticket)))
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
            'message' => 'Lo stato del ticket è cambiato da ' . $this->oldStatus . ' a ' . $this->ticket->status,
        ];
    }
}