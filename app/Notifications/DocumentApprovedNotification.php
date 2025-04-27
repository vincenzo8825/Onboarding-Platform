<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $document;
    protected $approver;

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document, User $approver)
    {
        $this->document = $document;
        $this->approver = $approver;
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
            ->subject('Documento Approvato')
            ->line('Il tuo documento "' . $this->document->title . '" è stato approvato.')
            ->action('Visualizza Documento', route('employee.documents.show', $this->document->id))
            ->line('Grazie per aver utilizzato la nostra piattaforma!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Documento Approvato',
            'message' => 'Il tuo documento "' . $this->document->title . '" è stato approvato da ' . $this->approver->name,
            'url' => route('employee.documents.show', $this->document->id),
            'document_id' => $this->document->id,
            'approver_id' => $this->approver->id,
        ];
    }
}