<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRequestNotification extends Notification
{
    use Queueable;

    protected $documentRequest;
    protected $requester;

    /**
     * Create a new notification instance.
     */
    public function __construct(DocumentRequest $documentRequest, User $requester)
    {
        $this->documentRequest = $documentRequest;
        $this->requester = $requester;
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
            ->subject('Richiesta di Documento: ' . $this->documentRequest->document_type)
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Ti è stato richiesto di caricare un documento: ' . $this->documentRequest->document_type)
            ->line($this->documentRequest->description)
            ->action('Visualizza Richiesta', url('/employee/documents'))
            ->line('Grazie per la collaborazione!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nuova richiesta di documento',
            'message' => 'L\'amministratore richiede il documento: ' . $this->documentRequest->document_type,
            'document_request_id' => $this->documentRequest->id,
            'requester_id' => $this->requester->id,
            'requester_name' => $this->requester->name,
            'due_date' => $this->documentRequest->due_date ? $this->documentRequest->due_date->format('d/m/Y') : null,
            'url' => '/employee/documents',
            'type' => 'document_request'
        ];
    }
}
