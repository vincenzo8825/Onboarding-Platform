<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $document;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document, User $user)
    {
        $this->document = $document;
        $this->user = $user;
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
        $url = route('admin.documents.show', $this->document);

        return (new MailMessage)
            ->subject('Nuovo documento caricato')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line($this->user->name . ' ha caricato un nuovo documento "' . $this->document->title . '" che richiede approvazione.')
            ->line('Categoria: ' . $this->document->category)
            ->action('Visualizza e approva', $url)
            ->line('Grazie per l\'utilizzo della nostra piattaforma!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    // Verifica che il metodo toArray() contenga tutti i dati necessari
    public function toArray($notifiable)
    {
        return [
            'title' => 'Nuovo documento caricato',
            'message' => "L'utente {$this->user->name} ha caricato un nuovo documento: {$this->document->title}",
            'url' => route('admin.documents.show', $this->document->id),
            'document_id' => $this->document->id,
            'user_id' => $this->user->id,
        ];
    }
}
