<?php

namespace App\Listeners;

use App\Events\EventCreated;
use App\Notifications\EventCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEventCreatedNotification implements ShouldQueue
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
    public function handle(EventCreated $event): void
    {
        // Recupera i dati dall'evento
        $eventObj = $event->event;
        $user = $event->user;
        $creator = $event->creator;

        // Invia la notifica all'utente invitato all'evento
        if ($user) {
            $user->notify(new EventCreatedNotification($eventObj, $creator));
        }
    }
}
