<?php

namespace App\Events;

use App\Models\ChecklistItem;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChecklistItemCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * L'utente che ha completato l'item
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * L'item della checklist completato
     *
     * @var \App\Models\ChecklistItem
     */
    public $checklistItem;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChecklistItem  $checklistItem
     * @return void
     */
    public function __construct(User $user, ChecklistItem $checklistItem)
    {
        $this->user = $user;
        $this->checklistItem = $checklistItem;
    }
}
