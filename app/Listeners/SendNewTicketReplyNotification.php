<?php

namespace App\Listeners;

use App\Events\TicketReplied;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewTicketReplyNotification;

class SendNewTicketReplyNotification implements ShouldQueue
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
    public function handle(TicketReplied $event): void
    {
        $ticket = $event->ticket;
        $reply = $event->reply;
        $user = $event->user;

        // Determina il destinatario della notifica
        // Se l'utente che ha risposto è l'admin, invia la notifica al creatore del ticket
        // Altrimenti, invia la notifica all'admin assegnato o a tutti gli admin
        if ($user->role === 'admin') {
            // Invia al creatore del ticket
            $ticket->creator->notify(new NewTicketReplyNotification($ticket, $reply));
        } else {
            // Invia all'admin assegnato o a tutti gli admin se non assegnato
            if ($ticket->assignedTo) {
                $ticket->assignedTo->notify(new NewTicketReplyNotification($ticket, $reply));
            } else {
                // Invia a tutti gli admin
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new NewTicketReplyNotification($ticket, $reply));
                }
            }
        }
    }
}
