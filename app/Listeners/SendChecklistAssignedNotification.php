<?php

namespace App\Listeners;

use App\Events\ChecklistAssigned;
use App\Notifications\ChecklistAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChecklistAssignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(ChecklistAssigned $event): void
    {
        // Invia la notifica all'utente a cui è stata assegnata la checklist
        $event->user->notify(new ChecklistAssignedNotification(
            $event->checklist,
            $event->admin
        ));
    }
}
