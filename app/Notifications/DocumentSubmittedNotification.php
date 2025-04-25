<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentSubmittedNotification extends Notification
{
    use Queueable;

    protected $documentRequest;
    protected $employee;

    /**
     * Create a new notification instance.
     */
    public function __construct(DocumentRequest $documentRequest, User $employee)
    {
        $this->documentRequest = $documentRequest;
        $this->employee = $employee;
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
            ->subject('Documento Caricato: ' . $this->documentRequest->document_type)
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Il dipendente ' . $this->employee->name . ' ha caricato il documento richiesto: ' . $this->documentRequest->document_type)
            ->action('Visualizza Documento', url('/admin/employees/' . $this->employee->id))
            ->line('Grazie!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Documento caricato',
            'message' => $this->employee->name . ' ha caricato il documento: ' . $this->documentRequest->document_type,
            'document_request_id' => $this->documentRequest->id,
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->name,
            'url' => '/admin/employees/' . $this->employee->id,
            'type' => 'document_submitted'
        ];
    }
}
