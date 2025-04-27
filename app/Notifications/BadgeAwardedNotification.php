<?php

namespace App\Notifications;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BadgeAwardedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $badge;
    protected $awardedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Badge $badge, User $awardedBy)
    {
        $this->badge = $badge;
        $this->awardedBy = $awardedBy;
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
                    ->subject('Hai ricevuto un nuovo badge!')
                    ->line('Congratulazioni! Hai ricevuto il badge "' . $this->badge->name . '".')
                    ->line('Descrizione: ' . $this->badge->description)
                    ->action('Visualizza i tuoi badge', route('employee.badges.index'))
                    ->line('Continua così!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nuovo Badge: ' . $this->badge->name,
            'message' => 'Hai ricevuto il badge "' . $this->badge->name . '" da ' . $this->awardedBy->name,
            'url' => route('employee.badges.index'),
            'badge_id' => $this->badge->id,
            'awarded_by' => $this->awardedBy->id,
        ];
    }
}
