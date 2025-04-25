<?php

namespace App\Notifications;

use App\Models\Checklist;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChecklistCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * L'utente che ha completato la checklist.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * La checklist completata.
     *
     * @var \App\Models\Checklist
     */
    protected $checklist;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Checklist  $checklist
     * @return void
     */
    public function __construct(User $user, Checklist $checklist)
    {
        $this->user = $user;
        $this->checklist = $checklist;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('admin.users.checklists.index', $this->user);

        return (new MailMessage)
            ->subject('Checklist completata')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line($this->user->name . ' ha completato la checklist "' . $this->checklist->name . '".')
            ->line('Tutti gli elementi della checklist sono stati completati.')
            ->action('Visualizza dettagli', $url)
            ->line('Grazie per l\'utilizzo della nostra piattaforma!');
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'checklist_id' => $this->checklist->id,
            'checklist_name' => $this->checklist->name,
            'message' => $this->user->name . ' ha completato la checklist "' . $this->checklist->name . '".',
            'url' => route('admin.users.checklists.index', $this->user)
        ];
    }
}
