<?php

namespace App\Notifications;

use App\Models\ChecklistItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChecklistItemStatusUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * L'admin che ha aggiornato lo stato.
     *
     * @var \App\Models\User
     */
    protected $admin;

    /**
     * L'elemento della checklist.
     *
     * @var \App\Models\ChecklistItem
     */
    protected $checklistItem;

    /**
     * Lo stato aggiornato dell'elemento.
     *
     * @var string
     */
    protected $status;

    /**
     * Le note aggiuntive (opzionali).
     *
     * @var string|null
     */
    protected $notes;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\User  $admin
     * @param  \App\Models\ChecklistItem  $checklistItem
     * @param  string  $status
     * @param  string|null  $notes
     * @return void
     */
    public function __construct(User $admin, ChecklistItem $checklistItem, string $status, ?string $notes = null)
    {
        $this->admin = $admin;
        $this->checklistItem = $checklistItem;
        $this->status = $status;
        $this->notes = $notes;
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
        $url = route('employee.checklists.show', $this->checklistItem->checklist_id);
        $mailMessage = (new MailMessage)
            ->greeting('Ciao ' . $notifiable->name . ',');

        if ($this->status === 'completed') {
            $mailMessage->subject('Elemento della checklist approvato')
                ->line('L\'elemento "' . $this->checklistItem->title . '" della checklist "' . $this->checklistItem->checklist->name . '" è stato approvato.');
        } else {
            $mailMessage->subject('Elemento della checklist rifiutato')
                ->line('L\'elemento "' . $this->checklistItem->title . '" della checklist "' . $this->checklistItem->checklist->name . '" è stato rifiutato.');
        }

        if ($this->notes) {
            $mailMessage->line('Note: ' . $this->notes);
        }

        return $mailMessage->action('Visualizza checklist', $url)
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
        $message = ($this->status === 'completed')
            ? 'L\'elemento "' . $this->checklistItem->title . '" della checklist "' . $this->checklistItem->checklist->name . '" è stato approvato.'
            : 'L\'elemento "' . $this->checklistItem->title . '" della checklist "' . $this->checklistItem->checklist->name . '" è stato rifiutato.';

        if ($this->notes) {
            $message .= ' Note: ' . $this->notes;
        }

        return [
            'admin_id' => $this->admin->id,
            'admin_name' => $this->admin->name,
            'checklist_item_id' => $this->checklistItem->id,
            'checklist_item_title' => $this->checklistItem->title,
            'checklist_id' => $this->checklistItem->checklist->id,
            'checklist_name' => $this->checklistItem->checklist->name,
            'status' => $this->status,
            'message' => $message,
            'url' => route('employee.checklists.show', $this->checklistItem->checklist_id)
        ];
    }
}
