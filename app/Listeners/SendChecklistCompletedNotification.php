<?php

namespace App\Listeners;

use App\Events\ChecklistCompleted;
use App\Notifications\ChecklistCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendChecklistCompletedNotification implements ShouldQueue
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
    public function handle(ChecklistCompleted $event): void
    {
        // Notifica agli admin che l'utente ha completato la checklist
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        Notification::send($admins, new ChecklistCompletedNotification(
            $event->user,
            $event->checklist
        ));
    }
}
