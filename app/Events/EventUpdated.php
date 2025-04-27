<?php

namespace App\Events;

use App\Models\Event;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $event;
    public $user;
    public $updatedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event, User $user, User $updatedBy)
    {
        $this->event = $event;
        $this->user = $user;
        $this->updatedBy = $updatedBy;
    }
}
