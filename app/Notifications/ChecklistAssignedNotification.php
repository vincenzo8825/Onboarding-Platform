<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Checklist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChecklistAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * La checklist assegnata.
     *
     * @var \App\Models\Checklist
     */
    protected Checklist $checklist;

    /**
     * L'utente admin che ha assegnato la checklist.
     *
     * @var \App\Models\User
     */
    protected User $admin;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Checklist  $checklist
     * @param  \App\Models\User  $admin
     * @return void
     */
    public function __construct(Checklist $checklist, User $admin)
    {
        $this->checklist = $checklist;
        $this->admin = $admin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuova checklist assegnata')
            ->greeting('Ciao ' . $notifiable->name . '!')
            ->line('Ti è stata assegnata una nuova checklist da completare.')
            ->line('Titolo: ' . $this->checklist->title)
            ->line('Assegnata da: ' . $this->admin->name)
            ->action('Visualizza Checklist', url('/checklists/' . $this->checklist->id))
            ->line('Ricordati di completare tutte le attività entro la data di scadenza.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Nuova checklist assegnata',
            'message' => 'Ti è stata assegnata la checklist "' . $this->checklist->title . '"',
            'admin_id' => $this->admin->id,
            'admin_name' => $this->admin->name,
            'checklist_id' => $this->checklist->id,
            'checklist_title' => $this->checklist->title,
            'type' => 'checklist',
            'url' => '/checklists/' . $this->checklist->id
        ];
    }
}
