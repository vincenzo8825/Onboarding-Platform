<?php

namespace App\Events;

use App\Models\Checklist;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChecklistCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * L'utente che ha completato la checklist.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * La checklist completata.
     *
     * @var \App\Models\Checklist
     */
    public $checklist;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Checklist  $checklist
     * @return void
     */
    public function __construct(User $user, Checklist $checklist)
    {
        $this->user = $user;
        $this->checklist = $checklist;
    }
}
