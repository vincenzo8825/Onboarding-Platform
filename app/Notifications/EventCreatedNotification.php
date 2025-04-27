<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $createdBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, User $createdBy)
    {
        $this->event = $event;
        $this->createdBy = $createdBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Nuovo Evento: ' . $this->event->title)
                    ->line('Sei stato invitato a un nuovo evento: "' . $this->event->title . '".')
                    ->line('Data inizio: ' . $this->event->start_date->format('d/m/Y H:i'))
                    ->line('Data fine: ' . $this->event->end_date->format('d/m/Y H:i'))
                    ->line('Luogo: ' . ($this->event->is_online ? 'Online' : $this->event->location))
                    ->action('Vedi dettagli evento', route('employee.events.show', $this->event->id))
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
            'title' => 'Nuovo Evento: ' . $this->event->title,
            'message' => 'Sei stato invitato all\'evento "' . $this->event->title . '" da ' . $this->createdBy->name . '. Data: ' . $this->event->start_date->format('d/m/Y'),
            'url' => route('employee.events.show', $this->event->id),
            'event_id' => $this->event->id,
            'created_by' => $this->createdBy->id,
        ];
    }
}
