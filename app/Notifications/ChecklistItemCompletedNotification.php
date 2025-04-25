<?php

namespace App\Notifications;

use App\Models\ChecklistItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChecklistItemCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * L'utente che ha completato l'elemento.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * L'elemento della checklist completato.
     *
     * @var \App\Models\ChecklistItem
     */
    protected $checklistItem;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChecklistItem  $checklistItem
     * @return void
     */
    public function __construct(User $user, ChecklistItem $checklistItem)
    {
        $this->user = $user;
        $this->checklistItem = $checklistItem;
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
            ->subject('Elemento della checklist completato')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line($this->user->name . ' ha completato l\'elemento "' . $this->checklistItem->title . '" della checklist "' . $this->checklistItem->checklist->name . '".')
            ->line('Questo elemento richiede la tua approvazione.')
            ->action('Visualizza e approva', $url)
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
            'checklist_item_id' => $this->checklistItem->id,
            'checklist_item_title' => $this->checklistItem->title,
            'checklist_id' => $this->checklistItem->checklist->id,
            'checklist_name' => $this->checklistItem->checklist->name,
            'message' => $this->user->name . ' ha completato l\'elemento "' . $this->checklistItem->title . '" della checklist "' . $this->checklistItem->checklist->name . '".',
            'url' => route('admin.users.checklists.index', $this->user)
        ];
    }
}
