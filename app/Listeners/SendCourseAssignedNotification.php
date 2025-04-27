<?php

namespace App\Listeners;

use App\Events\CourseAssigned;
use App\Notifications\CourseAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCourseAssignedNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CourseAssigned $event): void
    {
        // Invia notifica all'utente a cui è stato assegnato il corso
        // Invia direttamente la notifica per assicurarsi che funzioni
        if ($event->user) {
            $event->user->notify(new CourseAssignedNotification(
                $event->course,
                $event->assignedBy
            ));
        }
    }
}
