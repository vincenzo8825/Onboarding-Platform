<?php

namespace App\Listeners;

use App\Events\ChecklistItemStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChecklistItemStatusUpdateNotification
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
    public function handle(ChecklistItemStatusUpdated $event): void
    {
        //
    }
}
