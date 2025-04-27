<?php

namespace App\Events;

use App\Models\Checklist;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChecklistAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La checklist assegnata.
     *
     * @var \App\Models\Checklist
     */
    public $checklist;

    /**
     * L'utente a cui è stata assegnata la checklist.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * L'admin che ha assegnato la checklist.
     *
     * @var \App\Models\User
     */
    public $admin;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Checklist  $checklist
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $admin
     * @return void
     */
    public function __construct(Checklist $checklist, User $user, User $admin)
    {
        $this->checklist = $checklist;
        $this->user = $user;
        $this->admin = $admin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
