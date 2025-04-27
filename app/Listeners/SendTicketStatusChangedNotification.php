<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TicketStatusChangedNotification;

class SendTicketStatusChangedNotification implements ShouldQueue
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
    public function handle(TicketStatusChanged $event): void
    {
        $ticket = $event->ticket;
        $status = $event->status;
        $user = $event->user;

        // Se l'utente che ha cambiato lo stato è admin, notifica il creatore del ticket
        if ($user->role === 'admin') {
            $ticket->creator->notify(new TicketStatusChangedNotification($ticket, $status));
        } else {
            // Se a fare il cambio è un utente normale, notifica l'admin assegnato o tutti gli admin
            if ($ticket->assignedTo) {
                $ticket->assignedTo->notify(new TicketStatusChangedNotification($ticket, $status));
            } else {
                // Notifica tutti gli admin
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new TicketStatusChangedNotification($ticket, $status));
                }
            }
        }
    }
}
