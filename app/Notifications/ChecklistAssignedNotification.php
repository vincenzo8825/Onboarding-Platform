<?php

namespace App\Notifications;

use App\Models\Checklist;
use App\Models\User;
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
    protected $checklist;

    /**
     * L'utente admin che ha assegnato la checklist.
     *
     * @var \App\Models\User
     */
    protected $admin;

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
        $url = route('employee.checklists.show', $this->checklist->id);

        return (new MailMessage)
            ->subject('Nuova checklist assegnata')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Ti è stata assegnata una nuova checklist: "' . $this->checklist->name . '".')
            ->line($this->checklist->description)
            ->action('Visualizza checklist', $url)
            ->line('Completala il prima possibile per procedere con il tuo onboarding.');
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
            'checklist_id' => $this->checklist->id,
            'checklist_name' => $this->checklist->name,
            'admin_id' => $this->admin->id,
            'admin_name' => $this->admin->name,
            'message' => 'Ti è stata assegnata una nuova checklist: "' . $this->checklist->name . '".',
            'url' => route('employee.checklists.show', $this->checklist->id)
        ];
    }
}