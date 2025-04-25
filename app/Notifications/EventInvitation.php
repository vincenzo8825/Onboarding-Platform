<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
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
        $eventDate = $this->event->start_date->format('d/m/Y H:i');
        $eventLocation = $this->event->location;

        return (new MailMessage)
            ->subject('Invito a un evento: ' . $this->event->title)
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Sei stato invitato a partecipare a un evento.')
            ->line('Titolo: ' . $this->event->title)
            ->line('Data: ' . $eventDate)
            ->line('Luogo: ' . $eventLocation)
            ->line('Tipo: ' . ucfirst($this->event->type))
            ->action('Visualizza Evento', url(route('employee.events.show', $this->event)))
            ->line('Potrai confermare la tua partecipazione dalla pagina dell\'evento.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'message' => 'Sei stato invitato a partecipare all\'evento "' . $this->event->title . '"',
            'type' => $this->event->type,
            'url' => route('employee.events.show', $this->event),
            'start_date' => $this->event->start_date->format('Y-m-d H:i:s'),
        ];
    }
}
