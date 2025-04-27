<?php

namespace App\Listeners;

use App\Events\ChecklistItemCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChecklistItemCompletedNotification
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
    public function handle(ChecklistItemCompleted $event): void
    {
        //
    }
}
