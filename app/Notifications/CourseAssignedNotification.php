<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;
    protected $assignedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, User $assignedBy)
    {
        $this->course = $course;
        $this->assignedBy = $assignedBy;
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
            ->subject('Nuovo Corso Assegnato')
            ->line('Ti è stato assegnato un nuovo corso: "' . $this->course->title . '".')
            ->action('Vai al Corso', route('employee.courses.show', $this->course->id))
            ->line('Completa il corso entro la data di scadenza se prevista.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nuovo Corso Assegnato',
            'message' => 'Ti è stato assegnato un nuovo corso: "' . $this->course->title . '" da ' . $this->assignedBy->name,
            'url' => route('employee.courses.show', $this->course->id),
            'course_id' => $this->course->id,
            'assigned_by' => $this->assignedBy->id,
        ];
    }
}