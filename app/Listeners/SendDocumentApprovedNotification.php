<?php

namespace App\Listeners;

use App\Events\DocumentApproved;
use App\Notifications\DocumentApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDocumentApprovedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(DocumentApproved $event): void
    {
        // Invia notifica all'utente che ha caricato il documento
        $event->document->user->notify(new DocumentApprovedNotification(
            $event->document,
            $event->approver
        ));
    }
}
