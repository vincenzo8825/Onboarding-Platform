<?php

namespace App\Listeners;

use App\Events\BadgeAwarded;
use App\Notifications\BadgeAwardedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBadgeAwardedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\BadgeAwarded  $event
     * @return void
     */
    public function handle(BadgeAwarded $event)
    {
        $badge = $event->badge;
        $user = $event->user;
        $awardedBy = $event->awardedBy;

        // Invia la notifica all'utente
        if ($user) {
            $user->notify(new BadgeAwardedNotification($badge, $awardedBy));
        }
    }
}
