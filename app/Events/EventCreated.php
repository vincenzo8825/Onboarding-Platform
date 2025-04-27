<?php

namespace App\Events;

use App\Models\Event;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $event;
    public $user;
    public $creator;

    /**
     * Create a new event instance.
     */
    public function __construct(Event $event, User $user, User $creator)
    {
        $this->event = $event;
        $this->user = $user;
        $this->creator = $creator;
    }
}
