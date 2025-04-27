<?php

namespace App\Listeners;

use App\Events\EventUpdated;
use App\Notifications\EventUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEventUpdatedNotification implements ShouldQueue
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
    public function handle(EventUpdated $event): void
    {
        // Recupera i dati dall'evento
        $eventObj = $event->event;
        $user = $event->user;
        $updater = $event->updatedBy;

        // Invia la notifica all'utente interessato
        if ($user) {
            $user->notify(new EventUpdatedNotification($eventObj, $updater));
        }
    }
}
